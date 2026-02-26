<?php
/**
 * Uninstall script for Consent Mode plugin.
 *
 * This file is called when the plugin is deleted via WordPress admin.
 * It removes all plugin data if the purge_on_uninstall option is enabled.
 *
 * @package ConsentMode
 */

// Exit if not called from WordPress.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

/**
 * Remove all plugin data if the user enabled the "purge on uninstall" option.
 *
 * No custom DB tables exist — the plugin is fully stateless (No-DB Policy).
 * Consent preferences live exclusively in the visitor's browser cookies.
 * The only server-side storage is the standard wp_options table.
 */
$purge_on_uninstall = get_option( 'consent_mode_purge_on_uninstall', false );

if ( $purge_on_uninstall ) {
	// Delete main settings array stored in wp_options.
	delete_option( 'consent_mode_settings' );

	// Delete the purge flag itself.
	delete_option( 'consent_mode_purge_on_uninstall' );

	// Delete any geo-detection transients (stored in wp_options, no custom tables).
	// Transients are prefixed per-session; bulk-remove with a SQL LIKE is not needed
	// because they expire automatically. We delete only the known named transients.
	delete_transient( 'consent_mode_geo_cache' );

	// Flush rewrite rules.
	flush_rewrite_rules();
}
