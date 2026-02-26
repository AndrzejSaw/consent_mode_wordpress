<?php
/**
 * Example settings configuration for RU Consent Mode plugin.
 *
 * This file demonstrates how to configure the plugin settings.
 * Copy these settings to your WordPress database or admin panel.
 *
 * @package RUConsentMode
 */

// Default plugin settings structure.
$ru_consent_mode_settings = [
	// Google Tag Manager Configuration.
	'inject_gtm_loader'  => true,              // Enable GTM injection.
	'gtm_container_id'   => 'GTM-XXXXXXX',     // Your GTM container ID.
	'gtm_auth'           => '',                // GTM auth parameter (optional, for environments).
	'gtm_preview'        => '',                // GTM preview parameter (optional).
	'gtm_cookies_win'    => '',                // GTM cookies_win parameter (optional).

	// Consent Mode Settings.
	'ads_data_redaction' => true,              // Enable ads data redaction.
	'url_passthrough'    => false,             // Enable URL passthrough for cross-domain.
	'wait_for_update'    => 500,               // Wait time for consent update (ms).

	// Banner Settings.
	'banner_enabled'     => true,              // Show consent banner.
	'banner_position'    => 'bottom',          // Banner position: 'bottom', 'top', 'modal'.
	'banner_text'        => [
		'en_US' => 'We use cookies to improve your experience. By continuing to browse, you agree to our use of cookies.',
		'ru_RU' => 'Мы используем файлы cookie для улучшения вашего опыта. Продолжая просмотр, вы соглашаетесь с использованием cookie.',
	],
	'accept_button_text' => [
		'en_US' => 'Accept All',
		'ru_RU' => 'Принять все',
	],
	'reject_button_text' => [
		'en_US' => 'Reject All',
		'ru_RU' => 'Отклонить все',
	],
	'settings_button_text' => [
		'en_US' => 'Settings',
		'ru_RU' => 'Настройки',
	],

	// Geolocation Settings.
	'geo_provider'       => 'cloudflare',      // Geolocation provider: 'cloudflare', 'maxmind', 'ipapi'.
	'geo_api_key'        => '',                // API key for geolocation service (if required).

	// Logging Settings.
	'enable_logging'     => true,              // Enable consent logging.
	'log_retention_days' => 365,               // How long to keep logs (days).

	// Advanced Settings.
	'purge_on_uninstall' => false,             // Delete all data on plugin uninstall.
	'cookie_expiry_days' => 365,               // Consent cookie expiration (days).
	'show_in_eu_only'    => false,             // Show banner only in EU/Russia.
];

// Save settings to database.
// update_option( 'ru_consent_mode_settings', $ru_consent_mode_settings );

/**
 * How to set settings programmatically:
 */

// Example 1: Update single setting.
// $settings = get_option( 'ru_consent_mode_settings', [] );
// $settings['gtm_container_id'] = 'GTM-ABC123';
// update_option( 'ru_consent_mode_settings', $settings );

// Example 2: Enable GTM with specific container.
// $settings = get_option( 'ru_consent_mode_settings', [] );
// $settings['inject_gtm_loader'] = true;
// $settings['gtm_container_id'] = 'GTM-ABC123';
// update_option( 'ru_consent_mode_settings', $settings );

/**
 * How to use filters to customize consent behavior:
 */

// Example 1: Customize default consent state.
add_filter( 'ru_consent_mode_default_consent', function( $consent, $country_code, $is_strict_region ) {
	// Grant analytics in non-strict regions by default.
	if ( ! $is_strict_region ) {
		$consent['analytics_storage'] = 'granted';
	}
	return $consent;
}, 10, 3 );

// Example 2: Modify consent types.
add_filter( 'ru_consent_mode_consent_types', function( $types ) {
	// Add custom consent type.
	$types['custom_storage'] = 'Custom Data Storage';
	return $types;
} );

/**
 * Example GTM container configurations:
 */

// Production environment.
$production_settings = [
	'inject_gtm_loader' => true,
	'gtm_container_id'  => 'GTM-XXXXXXX',
	'gtm_auth'          => '',
	'gtm_preview'       => '',
	'gtm_cookies_win'   => '',
];

// Staging environment.
$staging_settings = [
	'inject_gtm_loader' => true,
	'gtm_container_id'  => 'GTM-XXXXXXX',
	'gtm_auth'          => 'your-auth-string',
	'gtm_preview'       => 'env-2',
	'gtm_cookies_win'   => 'x',
];

// Development environment.
$development_settings = [
	'inject_gtm_loader' => true,
	'gtm_container_id'  => 'GTM-XXXXXXX',
	'gtm_auth'          => 'your-auth-string',
	'gtm_preview'       => 'env-1',
	'gtm_cookies_win'   => 'x',
];

/**
 * Testing the configuration:
 */

// 1. Check if consent mode is initialized:
//    - Open browser console
//    - Look for: "[RU Consent Mode] Default consent state initialized"
//    - Check window.dataLayer array

// 2. Check if GTM is loaded:
//    - Open browser console
//    - Check window.google_tag_manager object
//    - Verify GTM container ID is present

// 3. Test consent update:
//    - Use browser console: gtag('consent', 'update', {analytics_storage: 'granted'})
//    - Check dataLayer for consent update event

// 4. Verify in GTM Preview mode:
//    - Open GTM Preview & Debug
//    - Check Consent Initialization event
//    - Verify consent state in Variables

/**
 * Debugging:
 */

// Enable WordPress debug mode in wp-config.php:
// define( 'WP_DEBUG', true );
// define( 'WP_DEBUG_LOG', true );
// define( 'WP_DEBUG_DISPLAY', false );

// Check debug.log for GTM-related errors:
// - Invalid or missing GTM container ID
// - Failed to load GTM container
// - Consent mode initialization issues

/**
 * Common GTM container ID formats:
 */

// Valid formats:
// - GTM-XXXXXXX (standard)
// - GTM-XXXX (older format)
// - GTM-XXXXXXXXX (newer format)

// Invalid formats:
// - GT-XXXXXXX (missing M)
// - GTM-XXXXXX- (trailing dash)
// - gtm-xxxxxxx (lowercase)

/**
 * Environment-specific configurations:
 */

// Use WordPress environment constants.
if ( defined( 'WP_ENVIRONMENT_TYPE' ) ) {
	switch ( WP_ENVIRONMENT_TYPE ) {
		case 'production':
			// Production GTM container.
			$gtm_id = 'GTM-PROD123';
			break;
		case 'staging':
			// Staging GTM container with environment params.
			$gtm_id = 'GTM-STAG123';
			break;
		case 'development':
		case 'local':
			// Development GTM container.
			$gtm_id = 'GTM-DEV123';
			break;
		default:
			$gtm_id = 'GTM-DEFAULT';
	}

	// Update settings based on environment.
	// $settings = get_option( 'ru_consent_mode_settings', [] );
	// $settings['gtm_container_id'] = $gtm_id;
	// update_option( 'ru_consent_mode_settings', $settings );
}
