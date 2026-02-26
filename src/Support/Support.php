<?php
/**
 * Support module for Consent Mode plugin.
 *
 * Provides helper functions and utilities used across the plugin.
 *
 * @package ConsentMode\Support
 */

namespace ConsentMode\Support;

/**
 * Support class.
 */
class Support {
	/**
	 * Singleton instance.
	 *
	 * @var Support|null
	 */
	private static $instance = null;

	/**
	 * Get singleton instance.
	 *
	 * @return Support
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
	 * Initialize the support module.
	 *
	 * @return void
	 */
	public function init() {
		// TODO: Register any global helper functions or filters.
	}

	/**
	 * Sanitize consent data.
	 *
	 * @param array $data Consent data array.
	 * @return array Sanitized consent data.
	 */
	public function sanitize_consent_data( $data ) {
		// TODO: Validate and sanitize consent data structure.
		// TODO: Ensure only valid consent types are included.
		// TODO: Sanitize values to 'granted' or 'denied'.
		return [];
	}

	/**
	 * Get plugin option with default fallback.
	 *
	 * @param string $key Option key.
	 * @param mixed  $default Default value.
	 * @return mixed Option value or default.
	 */
	public function get_option( $key, $default = null ) {
		// TODO: Get option from wp_options table.
		// TODO: Return default if option doesn't exist.
		return $default;
	}

	/**
	 * Update plugin option.
	 *
	 * @param string $key Option key.
	 * @param mixed  $value Option value.
	 * @return bool Success status.
	 */
	public function update_option( $key, $value ) {
		// TODO: Update option in wp_options table.
		// TODO: Return success status.
		return false;
	}

	/**
	 * Check if current request is AJAX.
	 *
	 * @return bool True if AJAX request.
	 */
	public function is_ajax() {
		return defined( 'DOING_AJAX' ) && DOING_AJAX;
	}

	/**
	 * Check if current user has admin capabilities.
	 *
	 * @return bool True if user can manage options.
	 */
	public function is_admin_user() {
		return current_user_can( 'manage_options' );
	}

	/**
	 * Get user's IP address.
	 *
	 * @return string IP address.
	 */
	public function get_user_ip() {
		// TODO: Get IP from various sources (CloudFlare, proxy headers, REMOTE_ADDR).
		// TODO: Validate IP address format.
		// TODO: Handle IPv6 addresses.
		$ip = '';

		if ( ! empty( $_SERVER['HTTP_CF_CONNECTING_IP'] ) ) {
			$ip = sanitize_text_field( wp_unslash( $_SERVER['HTTP_CF_CONNECTING_IP'] ) );
		} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			$ip = sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_FORWARDED_FOR'] ) );
		} elseif ( ! empty( $_SERVER['REMOTE_ADDR'] ) ) {
			$ip = sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) );
		}

		return $ip;
	}
}
