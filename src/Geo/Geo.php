<?php
/**
 * Geo module for Consent Mode plugin.
 *
 * Handles geolocation detection to determine user's country for consent requirements.
 *
 * @package ConsentMode\Geo
 */

namespace ConsentMode\Geo;

/**
 * Geo class.
 */
class Geo {
	/**
	 * Singleton instance.
	 *
	 * @var Geo|null
	 */
	private static $instance = null;

	/**
	 * Get singleton instance.
	 *
	 * @return Geo
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Private constructor to prevent direct instantiation.
	 */
	private function __construct() {
		// Constructor logic here.
	}

	/**
	 * Initialize the geo module.
	 *
	 * @return void
	 */
	public function init() {
		// TODO: Hook into early WordPress actions for geolocation.
	}

	/**
	 * Get user's country code.
	 *
	 * @return string Country code (e.g., 'RU', 'US').
	 */
	public function get_country_code() {
		// Check for cached country code in transient.
		$cached_country = get_transient( 'consent_mode_country_' . $this->get_user_ip_hash() );
		if ( false !== $cached_country ) {
			return $cached_country;
		}

		$country_code = '';

		// Method 1: Check CloudFlare headers.
		if ( isset( $_SERVER['HTTP_CF_IPCOUNTRY'] ) ) {
			$country_code = strtoupper( sanitize_text_field( wp_unslash( $_SERVER['HTTP_CF_IPCOUNTRY'] ) ) );
		}

		// Method 2: Check other common CDN headers.
		if ( empty( $country_code ) && isset( $_SERVER['HTTP_X_COUNTRY_CODE'] ) ) {
			$country_code = strtoupper( sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_COUNTRY_CODE'] ) ) );
		}

		// Method 3: Use WordPress timezone setting as fallback.
		if ( empty( $country_code ) ) {
			$timezone = get_option( 'timezone_string' );
			if ( false !== strpos( $timezone, 'Europe/Moscow' ) ) {
				$country_code = 'RU';
			} elseif ( false !== strpos( $timezone, 'Europe/' ) ) {
				$country_code = 'EU'; // Generic EU for European timezones.
			}
		}

		// Cache result for 24 hours.
		if ( ! empty( $country_code ) ) {
			set_transient( 'consent_mode_country_' . $this->get_user_ip_hash(), $country_code, DAY_IN_SECONDS );
		}

		return $country_code;
	}

	/**
	 * Get hashed user IP for caching.
	 *
	 * @return string Hashed IP address.
	 */
	private function get_user_ip_hash() {
		$ip = '';
		if ( isset( $_SERVER['REMOTE_ADDR'] ) ) {
			$ip = sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) );
		}
		return md5( $ip );
	}

	/**
	 * Check if user is in Russia.
	 *
	 * @return bool True if user is in Russia.
	 */
	public function is_russia() {
		return 'RU' === $this->get_country_code();
	}

	/**
	 * Check if user is in EU/EEA.
	 *
	 * @return bool True if user is in EU/EEA country.
	 */
	public function is_eu() {
		$eea_countries = [
			'AT', 'BE', 'BG', 'HR', 'CY', 'CZ', 'DK', 'EE', 'FI', 'FR',
			'DE', 'GR', 'HU', 'IE', 'IT', 'LV', 'LT', 'LU', 'MT', 'NL',
			'PL', 'PT', 'RO', 'SK', 'SI', 'ES', 'SE', 'NO', 'IS', 'LI',
			'GB', // UK often treated similarly
		];
		return in_array( $this->get_country_code(), $eea_countries, true );
	}

	/**
	 * Alias for is_eu().
	 *
	 * @return bool
	 */
	public function is_eea() {
		return $this->is_eu();
	}

	/**
	 * Check if strict mode should be applied (GDPR).
	 *
	 * @return bool
	 */
	public function is_strict_mode() {
		// For now, we default to strict mode for safety in Poland, Ukraine, Belarus.
		// Or if user is in EEA.
		$strict_countries = [ 'PL', 'UA', 'BY', 'RU' ];
		$country = $this->get_country_code();
		
		if ( in_array( $country, $strict_countries, true ) ) {
			return true;
		}

		return $this->is_eu();
	}

	/**
	 * Get country from IP address.
	 *
	 * @param string $ip IP address.
	 * @return string Country code.
	 */
	private function get_country_from_ip( $ip ) {
		// TODO: Implement IP geolocation using free or paid service.
		// TODO: Consider using MaxMind GeoIP2, ip-api.com, or similar.
		// TODO: Handle API errors gracefully.
		return '';
	}
}
