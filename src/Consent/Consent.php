<?php
/**
 * Consent module for Consent Mode plugin.
 *
 * Stateless server-side consent reader. Consent preferences are stored
 * exclusively in the visitor's browser via the consent_preferences cookie.
 * No database tables are created or used (No-DB Policy).
 *
 * Typical PHP usage in themes or other plugins:
 *
 *   if ( \ConsentMode\Consent\Consent::instance()->has_consent( 'analytics_storage' ) ) {
 *       // Render inline analytics code here.
 *   }
 *
 * @package ConsentMode\Consent
 */

namespace ConsentMode\Consent;

/**
 * Consent class.
 *
 * Provides server-side reading and validation of the consent_preferences
 * cookie that is set client-side by the banner JavaScript.
 */
class Consent {

	/**
	 * The cookie name used for storing consent preferences.
	 * Must match the name configured in Front::enqueue_assets().
	 */
	const COOKIE_NAME = 'consent_preferences';

	/**
	 * All recognised GCMv2 consent parameters.
	 *
	 * @var string[]
	 */
	const CONSENT_PARAMS = [
		'ad_storage',
		'ad_user_data',
		'ad_personalization',
		'analytics_storage',
		'functionality_storage',
		'personalization_storage',
		'security_storage',
	];

	/**
	 * Parameters that are always granted regardless of user choice
	 * (Legitimate Interest / technical necessity).
	 *
	 * @var string[]
	 */
	const ALWAYS_GRANTED = [
		'functionality_storage',
		'security_storage',
	];

	/**
	 * Singleton instance.
	 *
	 * @var Consent|null
	 */
	private static $instance = null;

	/**
	 * In-request cache of the parsed consent array.
	 *
	 * @var array|null
	 */
	private $consent_cache = null;

	/**
	 * Get singleton instance.
	 *
	 * @return Consent
	 */
	public static function instance(): Consent {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Private constructor – use instance() to obtain the singleton.
	 */
	private function __construct() {}

	/**
	 * Initialize the consent module.
	 *
	 * Registers WP hooks if any future server-side processing is needed.
	 * Currently stateless – no hooks required.
	 *
	 * @return void
	 */
	public function init(): void {}

	/**
	 * Get the current visitor's consent state as an associative array.
	 *
	 * Reads and validates the consent_preferences cookie. Falls back to
	 * strict-privacy defaults (all denied) when:
	 *  - the cookie is absent (first visit),
	 *  - the cookie value is not valid JSON, or
	 *  - the cookie contains unrecognised values.
	 *
	 * @return array<string, string> Map of consent parameter → 'granted'|'denied'.
	 */
	public function get_consent(): array {
		// Return in-request cache when available.
		if ( null !== $this->consent_cache ) {
			return $this->consent_cache;
		}

		// No cookie → strict defaults (GDPR first-visit principle).
		if ( ! isset( $_COOKIE[ self::COOKIE_NAME ] ) ) {
			$this->consent_cache = $this->get_strict_defaults();
			return $this->consent_cache;
		}

		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$raw = json_decode( wp_unslash( $_COOKIE[ self::COOKIE_NAME ] ), true );

		if ( ! is_array( $raw ) ) {
			$this->consent_cache = $this->get_strict_defaults();
			return $this->consent_cache;
		}

		$this->consent_cache = $this->sanitize_consent( $raw );
		return $this->consent_cache;
	}

	/**
	 * Check whether the visitor has granted consent for a specific parameter.
	 *
	 * Example:
	 *   $consent->has_consent( 'analytics_storage' ); // true|false
	 *
	 * @param string $param One of the CONSENT_PARAMS values.
	 * @return bool True when the parameter is 'granted'.
	 */
	public function has_consent( string $param ): bool {
		$consent = $this->get_consent();
		return ( $consent[ $param ] ?? 'denied' ) === 'granted';
	}

	/**
	 * Check whether the visitor has made any consent choice at all.
	 *
	 * Returns false on the first visit (no cookie present).
	 *
	 * @return bool True if the consent cookie exists and is parseable.
	 */
	public function has_made_choice(): bool {
		if ( ! isset( $_COOKIE[ self::COOKIE_NAME ] ) ) {
			return false;
		}

		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$raw = json_decode( wp_unslash( $_COOKIE[ self::COOKIE_NAME ] ), true );

		return is_array( $raw );
	}

	/**
	 * Invalidate the in-request consent cache.
	 *
	 * Call this after programmatically modifying the consent cookie (rare).
	 *
	 * @return void
	 */
	public function flush_cache(): void {
		$this->consent_cache = null;
	}

	// -------------------------------------------------------------------------
	// Private helpers
	// -------------------------------------------------------------------------

	/**
	 * Return strict-privacy defaults: everything denied, necessities granted.
	 *
	 * This is the fallback state for first-time visitors and complies with
	 * GDPR's opt-in requirement.
	 *
	 * @return array<string, string>
	 */
	private function get_strict_defaults(): array {
		$defaults = [];

		foreach ( self::CONSENT_PARAMS as $param ) {
			$defaults[ $param ] = in_array( $param, self::ALWAYS_GRANTED, true ) ? 'granted' : 'denied';
		}

		return $defaults;
	}

	/**
	 * Sanitize raw cookie data: allow only known params and valid values.
	 *
	 * Unknown parameters are dropped; invalid values default to 'denied'.
	 * Parameters in ALWAYS_GRANTED are forced to 'granted'.
	 *
	 * @param array $raw Raw decoded cookie array.
	 * @return array<string, string> Sanitized consent array.
	 */
	private function sanitize_consent( array $raw ): array {
		$sanitized = $this->get_strict_defaults(); // Start from safe defaults.

		foreach ( self::CONSENT_PARAMS as $param ) {
			// Always-granted params cannot be overridden by the cookie.
			if ( in_array( $param, self::ALWAYS_GRANTED, true ) ) {
				continue;
			}

			if ( isset( $raw[ $param ] ) && 'granted' === $raw[ $param ] ) {
				$sanitized[ $param ] = 'granted';
			}
			// Otherwise it remains 'denied' from get_strict_defaults().
		}

		return $sanitized;
	}
}
