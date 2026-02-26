<?php
/**
 * Log module for Consent Mode plugin.
 *
 * Stateless implementation — no custom database tables are created or used
 * (No-DB Policy). Consent preferences are stored exclusively in the visitor's
 * browser via the consent_preferences cookie.
 *
 * This class provides optional debug-level output through the standard PHP
 * error_log() when WP_DEBUG is enabled. In production all log calls are
 * transparent no-ops, keeping zero server-side persistence of consent events.
 *
 * @package ConsentMode\Log
 */

namespace ConsentMode\Log;

/**
 * Log class.
 *
 * Lightweight, no-DB logger for development and debugging purposes.
 */
class Log {

	/**
	 * Log level constants.
	 */
	const LEVEL_DEBUG   = 'DEBUG';
	const LEVEL_INFO    = 'INFO';
	const LEVEL_WARNING = 'WARNING';
	const LEVEL_ERROR   = 'ERROR';

	/**
	 * Prefix added to every log entry for easy filtering.
	 *
	 * @var string
	 */
	private const LOG_PREFIX = '[Consent Mode]';

	/**
	 * Singleton instance.
	 *
	 * @var Log|null
	 */
	private static $instance = null;

	/**
	 * Get singleton instance.
	 *
	 * @return Log
	 */
	public static function instance(): Log {
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
	 * Initialize the log module.
	 *
	 * No WordPress hooks are required for the stateless implementation.
	 *
	 * @return void
	 */
	public function init(): void {}

	/**
	 * Log a consent change event.
	 *
	 * When WP_DEBUG is true the event is written to the PHP error log so
	 * developers can verify consent flow during development. In production
	 * this method is a silent no-op — consent events are NOT persisted to
	 * any database table (No-DB Policy).
	 *
	 * @param array  $consent_data Associative array of GCMv2 consent parameters.
	 * @param string $context      Optional context label (e.g. 'accept_all', 'essential').
	 * @return void
	 */
	public function log_consent( array $consent_data, string $context = '' ): void {
		if ( ! defined( 'WP_DEBUG' ) || ! WP_DEBUG ) {
			return;
		}

		$this->write(
			sprintf( 'Consent update [%s]: %s', $context ?: 'unknown', wp_json_encode( $consent_data ) ),
			self::LEVEL_INFO
		);
	}

	/**
	 * Write a generic message to the PHP error log.
	 *
	 * Only active when WP_DEBUG is true.
	 *
	 * @param string $message Human-readable message.
	 * @param string $level   One of the LEVEL_* class constants.
	 * @param array  $context Optional key-value pairs appended as JSON.
	 * @return void
	 */
	public function write( string $message, string $level = self::LEVEL_DEBUG, array $context = [] ): void {
		if ( ! defined( 'WP_DEBUG' ) || ! WP_DEBUG ) {
			return;
		}

		$entry = sprintf( '%s [%s] %s', self::LOG_PREFIX, $level, $message );

		if ( ! empty( $context ) ) {
			$entry .= ' | ' . wp_json_encode( $context );
		}

		// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
		error_log( $entry );
	}
}
