<?php
/**
 * Plugin Name: Universal Consent Mode
 * Plugin URI: https://example.com/universal-consent-mode
 * Description: Universal WordPress plugin for Google Consent Mode v2 with multilingual support and GDPR compliance for EU/EEA.
 * Version: 1.0.0
 * Requires at least: 6.2
 * Requires PHP: 8.1
 * Author: Your Name
 * Author URI: https://example.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: ru-consent-mode
 * Domain Path: /languages
 *
 * @package RUConsentMode
 */

namespace RUConsentMode;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Plugin version.
 */
define( 'RU_CONSENT_MODE_VERSION', '1.0.0' );

/**
 * Plugin directory path.
 */
define( 'RU_CONSENT_MODE_DIR', plugin_dir_path( __FILE__ ) );

/**
 * Plugin directory URL.
 */
define( 'RU_CONSENT_MODE_URL', plugin_dir_url( __FILE__ ) );

/**
 * Plugin basename.
 */
define( 'RU_CONSENT_MODE_BASENAME', plugin_basename( __FILE__ ) );

/**
 * Autoloader for plugin classes.
 *
 * @return void
 */
function autoload() {
	$autoload_file = RU_CONSENT_MODE_DIR . 'vendor/autoload.php';
	
	if ( file_exists( $autoload_file ) ) {
		require_once $autoload_file;
	} else {
		// Fallback manual autoloader if composer autoload is not available.
		spl_autoload_register(
			function ( $class ) {
				$prefix   = 'RUConsentMode\\';
				$base_dir = RU_CONSENT_MODE_DIR . 'src/';

				$len = strlen( $prefix );
				if ( strncmp( $prefix, $class, $len ) !== 0 ) {
					return;
				}

				$relative_class = substr( $class, $len );
				$file           = $base_dir . str_replace( '\\', '/', $relative_class ) . '.php';

				if ( file_exists( $file ) ) {
					require $file;
				}
			}
		);
	}
}

autoload();

/**
 * Initialize plugin modules.
 *
 * @return void
 */
function init_modules() {
	// Initialize Bootstrap module for Google Consent Mode v2 (must be first).
	Consent\Bootstrap::instance()->init();

	// Initialize Admin module for backend settings and options.
	Admin\Admin::instance()->init();

	// Initialize Front module for frontend banner display.
	Front\Front::instance()->init();

	// Initialize Consent module for consent management.
	Consent\Consent::instance()->init();

	// Initialize Geo module for geolocation detection.
	Geo\Geo::instance()->init();

	// TODO: Initialize Log module for consent logging.
	// Log\Log::instance()->init();

	// TODO: Initialize Support module for helper functions.
	// Support\Support::instance()->init();
}

add_action( 'plugins_loaded', __NAMESPACE__ . '\\init_modules' );

/**
 * Plugin activation hook.
 *
 * @return void
 */
function activate() {
	// TODO: Create necessary database tables for consent logs.
	// TODO: Set default plugin options.
	// TODO: Schedule cron jobs if needed.
	// TODO: Check for minimum PHP and WordPress versions.
	
	// Flush rewrite rules if needed.
	flush_rewrite_rules();
}

register_activation_hook( __FILE__, __NAMESPACE__ . '\\activate' );

/**
 * Plugin deactivation hook.
 *
 * @return void
 */
function deactivate() {
	// TODO: Clear scheduled cron jobs.
	// TODO: Flush rewrite rules.
	// TODO: Perform cleanup operations (but don't delete data yet).
	
	flush_rewrite_rules();
}

register_deactivation_hook( __FILE__, __NAMESPACE__ . '\\deactivate' );

/**
 * Load plugin text domain for translations.
 *
 * @return void
 */
function load_textdomain() {
	load_plugin_textdomain(
		'ru-consent-mode',
		false,
		dirname( RU_CONSENT_MODE_BASENAME ) . '/languages'
	);
}

add_action( 'init', __NAMESPACE__ . '\\load_textdomain' );
