<?php
/**
 * Uninstall script for RU Consent Mode plugin.
 *
 * This file is called when the plugin is deleted via WordPress admin.
 * It removes all plugin data if the purge_on_uninstall option is enabled.
 *
 * @package RUConsentMode
 */

// Exit if not called from WordPress.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

/**
 * Check if user wants to purge all data on uninstall.
 */
$purge_on_uninstall = get_option( 'ru_consent_mode_purge_on_uninstall', false );

if ( $purge_on_uninstall ) {
	// TODO: Delete all plugin options from wp_options table.
	// delete_option( 'ru_consent_mode_settings' );
	// delete_option( 'ru_consent_mode_purge_on_uninstall' );
	// TODO: Add deletion for other plugin options.

	// TODO: Drop custom database tables for consent logs.
	global $wpdb;
	// $table_name = $wpdb->prefix . 'ru_consent_logs';
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.SchemaChange
	// $wpdb->query( "DROP TABLE IF EXISTS {$table_name}" );

	// TODO: Delete all plugin transients.
	// delete_transient( 'ru_consent_mode_geo_cache' );

	// TODO: Delete all plugin user meta.
	// delete_metadata( 'user', 0, 'ru_consent_mode_preferences', '', true );

	// TODO: Clear any scheduled cron events.
	// wp_clear_scheduled_hook( 'ru_consent_mode_cleanup' );

	// TODO: Remove uploaded files from wp-content/uploads if any.

	// Flush rewrite rules.
	flush_rewrite_rules();
}

// TODO: Consider adding a cleanup function that runs regardless of purge_on_uninstall
// to remove temporary data, caches, etc.
