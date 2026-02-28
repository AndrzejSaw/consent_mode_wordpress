<?php
/**
 * Plugin Name: Universal Consent Mode
 * Plugin URI: https://example.com/universal-consent-mode
 * Description: Universal WordPress plugin for Google Consent Mode v2 with multilingual support (EN, RU, UA, PL) and GDPR/PKE compliance for EU/EEA.
 * Version: 1.1.4
 * Requires at least: 6.2
 * Requires PHP: 8.1
 * Author: Your Name
 * Author URI: https://example.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: consent-mode
 * Domain Path: /languages
 *
 * @package ConsentMode
 */

namespace ConsentMode;

// ИМПОРТ ДОЛЖЕН БЫТЬ ЗДЕСЬ - на самом верху!
use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Plugin version.
 */
define( 'CONSENT_MODE_VERSION', '1.1.4' );

/**
 * Plugin directory path.
 */
define( 'CONSENT_MODE_DIR', plugin_dir_path( __FILE__ ) );

/**
 * Plugin directory URL.
 */
define( 'CONSENT_MODE_URL', plugin_dir_url( __FILE__ ) );

/**
 * Plugin basename.
 */
define( 'CONSENT_MODE_BASENAME', plugin_basename( __FILE__ ) );

/**
 * Autoloader for plugin classes.
 *
 * @return void
 */
function autoload() {
    $autoload_file = CONSENT_MODE_DIR . 'vendor/autoload.php';

    if ( file_exists( $autoload_file ) ) {
        require_once $autoload_file;
    } else {
        // Fallback manual autoloader if Composer autoload is not available.
        spl_autoload_register(
            function ( $class ) {
                $prefix   = 'ConsentMode\\';
                $base_dir = CONSENT_MODE_DIR . 'src/';

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

// --- НАСТРОЙКА ОБНОВЛЕНИЙ ЧЕРЕЗ GITHUB ---

// Добавим проверку class_exists, чтобы плагин не "падал", если папка vendor не загрузилась
if ( class_exists( PucFactory::class ) ) {
    $myUpdateChecker = PucFactory::buildUpdateChecker(
        'https://github.com/AndrzejSaw/consent_mode_wordpress/',
        __FILE__,
        'consent-mode'
    );

    // Указываем, что скачивать нужно именно готовый ZIP из релизов
    $myUpdateChecker->getVcsApi()->enableReleaseAssets();

    // Так как репозиторий приватный, берем токен из wp-config.php клиентского сайта
    if ( defined( 'MY_GH_TOKEN' ) ) {
        $myUpdateChecker->setAuthentication( MY_GH_TOKEN );
    }
}

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

	// Initialize Log module (stateless – no database operations).
	Log\Log::instance()->init();
}

add_action( 'plugins_loaded', __NAMESPACE__ . '\\init_modules' );

/**
 * Plugin activation hook.
 *
 * @return void
 */
function activate() {
	// Set default plugin options if not already present.
	if ( ! get_option( 'consent_mode_settings' ) ) {
		update_option(
			'consent_mode_settings',
			[
				'inject_gtm_loader' => false,
				'gtm_container_id'  => '',
				'categories_map'    => [
					'analytics'  => '',
					'ads'        => '',
					'functional' => '',
				],
				'content'           => [],
			]
		);
	}

	// No database tables are created (No-DB Policy).
	// Consent preferences are stored exclusively in browser cookies.
	flush_rewrite_rules();
}

register_activation_hook( __FILE__, __NAMESPACE__ . '\\activate' );

/**
 * Plugin deactivation hook.
 *
 * @return void
 */
function deactivate() {
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
		'consent-mode',
		false,
		dirname( CONSENT_MODE_BASENAME ) . '/languages'
	);
}

add_action( 'init', __NAMESPACE__ . '\\load_textdomain' );
