<?php
/**
 * Admin module for RU Consent Mode plugin.
 *
 * Handles admin panel functionality, settings pages, and backend operations.
 *
 * @package RUConsentMode\Admin
 */

namespace RUConsentMode\Admin;

/**
 * Admin class.
 */
class Admin {
	/**
	 * Singleton instance.
	 *
	 * @var Admin|null
	 */
	private static $instance = null;

	/**
	 * Get singleton instance.
	 *
	 * @return Admin
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
	 * Initialize the admin module.
	 *
	 * @return void
	 */
	public function init() {
		// Register admin menu pages.
		add_action( 'admin_menu', [ $this, 'register_menu' ] );

		// Register settings and options.
		add_action( 'admin_init', [ $this, 'register_settings' ] );

		// TODO: Enqueue admin scripts and styles.
		// add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_assets' ] );

		// TODO: Add settings link to plugins page.
		// add_filter( 'plugin_action_links_' . RU_CONSENT_MODE_BASENAME, [ $this, 'add_settings_link' ] );

		// TODO: Handle AJAX requests for admin operations.
		// add_action( 'wp_ajax_ru_consent_mode_save_settings', [ $this, 'save_settings' ] );
	}

	/**
	 * Register admin menu pages.
	 *
	 * @return void
	 */
	public function register_menu() {
		add_options_page(
			__( 'RU Consent Mode Settings', 'ru-consent-mode' ),
			__( 'RU Consent Mode', 'ru-consent-mode' ),
			'manage_options',
			'ru-consent-mode-settings',
			[ $this, 'render_settings_page' ]
		);
	}

	/**
	 * Register plugin settings.
	 *
	 * @return void
	 */
	public function register_settings() {
		// Register main settings.
		register_setting(
			'ru_consent_mode_settings_group',
			'ru_consent_mode_settings',
			[ $this, 'sanitize_settings' ]
		);

		// GTM Settings Section.
		add_settings_section(
			'ru_consent_mode_gtm_section',
			__( 'Google Tag Manager Settings', 'ru-consent-mode' ),
			[ $this, 'render_gtm_section' ],
			'ru-consent-mode-settings'
		);

		// Script Guard Settings Section.
		add_settings_section(
			'ru_consent_mode_script_guard_section',
			__( 'Script Guard Settings', 'ru-consent-mode' ),
			[ $this, 'render_script_guard_section' ],
			'ru-consent-mode-settings'
		);

		// Multilingual Content Section.
		add_settings_section(
			'ru_consent_mode_content_section',
			__( 'Multilingual Content', 'ru-consent-mode' ),
			[ $this, 'render_content_section' ],
			'ru-consent-mode-settings'
		);

		// GTM: Enable GTM Loader.
		add_settings_field(
			'inject_gtm_loader',
			__( 'Enable GTM Loader', 'ru-consent-mode' ),
			[ $this, 'render_checkbox_field' ],
			'ru-consent-mode-settings',
			'ru_consent_mode_gtm_section',
			[
				'label_for' => 'inject_gtm_loader',
				'name'      => 'ru_consent_mode_settings[inject_gtm_loader]',
				'value'     => $this->get_option( 'inject_gtm_loader', false ),
			]
		);

		// GTM: Container ID.
		add_settings_field(
			'gtm_container_id',
			__( 'GTM Container ID', 'ru-consent-mode' ),
			[ $this, 'render_text_field' ],
			'ru-consent-mode-settings',
			'ru_consent_mode_gtm_section',
			[
				'label_for'   => 'gtm_container_id',
				'name'        => 'ru_consent_mode_settings[gtm_container_id]',
				'value'       => $this->get_option( 'gtm_container_id', '' ),
				'placeholder' => 'GTM-XXXXXXX',
			]
		);

		// Script Guard: Analytics Category.
		add_settings_field(
			'categories_map_analytics',
			__( 'Analytics Scripts', 'ru-consent-mode' ),
			[ $this, 'render_textarea_field' ],
			'ru-consent-mode-settings',
			'ru_consent_mode_script_guard_section',
			[
				'label_for'   => 'categories_map_analytics',
				'name'        => 'ru_consent_mode_settings[categories_map][analytics]',
				'value'       => $this->get_category_map( 'analytics' ),
				'description' => __( 'Comma-separated list of script handles to block until analytics consent is granted. Example: ga4, clarity, matomo', 'ru-consent-mode' ),
			]
		);

		// Script Guard: Ads Category.
		add_settings_field(
			'categories_map_ads',
			__( 'Advertising Scripts', 'ru-consent-mode' ),
			[ $this, 'render_textarea_field' ],
			'ru-consent-mode-settings',
			'ru_consent_mode_script_guard_section',
			[
				'label_for'   => 'categories_map_ads',
				'name'        => 'ru_consent_mode_settings[categories_map][ads]',
				'value'       => $this->get_category_map( 'ads' ),
				'description' => __( 'Comma-separated list of script handles to block until advertising consent is granted. Example: googletag, fb-pixel, adsbygoogle', 'ru-consent-mode' ),
			]
		);

		// Script Guard: Functional Category.
		add_settings_field(
			'categories_map_functional',
			__( 'Functional Scripts', 'ru-consent-mode' ),
			[ $this, 'render_textarea_field' ],
			'ru-consent-mode-settings',
			'ru_consent_mode_script_guard_section',
			[
				'label_for'   => 'categories_map_functional',
				'name'        => 'ru_consent_mode_settings[categories_map][functional]',
				'value'       => $this->get_category_map( 'functional' ),
				'description' => __( 'Comma-separated list of script handles for functional purposes. Example: youtube, vimeo, maps', 'ru-consent-mode' ),
			]
		);
		// Register Content Fields for each language.
		$languages = [
			'en' => 'English',
			'ru' => 'Russian',
			'pl' => 'Polish',
		];

		foreach ( $languages as $lang_code => $lang_name ) {
			// Title.
			add_settings_field(
				"content_title_{$lang_code}",
				sprintf( __( 'Banner Title (%s)', 'ru-consent-mode' ), $lang_name ),
				[ $this, 'render_text_field' ],
				'ru-consent-mode-settings',
				'ru_consent_mode_content_section',
				[
					'label_for' => "content_title_{$lang_code}",
					'name'      => "ru_consent_mode_settings[content][{$lang_code}][title]",
					'value'     => $this->get_content_option( $lang_code, 'title' ),
				]
			);

			// Description.
			add_settings_field(
				"content_desc_{$lang_code}",
				sprintf( __( 'Banner Description (%s)', 'ru-consent-mode' ), $lang_name ),
				[ $this, 'render_textarea_field' ],
				'ru-consent-mode-settings',
				'ru_consent_mode_content_section',
				[
					'label_for' => "content_desc_{$lang_code}",
					'name'      => "ru_consent_mode_settings[content][{$lang_code}][description]",
					'value'     => $this->get_content_option( $lang_code, 'description' ),
				]
			);

			// Privacy Policy URL.
			add_settings_field(
				"content_privacy_{$lang_code}",
				sprintf( __( 'Privacy Policy URL (%s)', 'ru-consent-mode' ), $lang_name ),
				[ $this, 'render_text_field' ],
				'ru-consent-mode-settings',
				'ru_consent_mode_content_section',
				[
					'label_for' => "content_privacy_{$lang_code}",
					'name'      => "ru_consent_mode_settings[content][{$lang_code}][privacy_url]",
					'value'     => $this->get_content_option( $lang_code, 'privacy_url' ),
				]
			);
		}
	}

	/**
	 * Render settings page.
	 *
	 * @return void
	 */
	public function render_settings_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'ru-consent-mode' ) );
		}
		?>
		<div class="wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
			<form method="post" action="options.php">
				<?php
				settings_fields( 'ru_consent_mode_settings_group' );
				do_settings_sections( 'ru-consent-mode-settings' );
				submit_button();
				?>
			</form>
		</div>
		<?php
	}

	/**
	 * Render GTM section description.
	 *
	 * @return void
	 */
	public function render_gtm_section() {
		echo '<p>' . esc_html__( 'Configure Google Tag Manager integration.', 'ru-consent-mode' ) . '</p>';
	}

	/**
	 * Render Script Guard section description.
	 *
	 * @return void
	 */
	public function render_script_guard_section() {
		echo '<p>' . esc_html__( 'Configure which scripts should be blocked until user consent is granted. Enter script handles as comma-separated values.', 'ru-consent-mode' ) . '</p>';
		echo '<p><strong>' . esc_html__( 'Priority Order:', 'ru-consent-mode' ) . '</strong> ' . esc_html__( 'If a script handle appears in multiple categories, the priority is: Ads > Analytics > Functional', 'ru-consent-mode' ) . '</p>';
	}

	/**
	 * Render Content section description.
	 *
	 * @return void
	 */
	public function render_content_section() {
		echo '<p>' . esc_html__( 'Customize the banner content for each supported language. If left empty, default translations will be used.', 'ru-consent-mode' ) . '</p>';
	}

	/**
	 * Render checkbox field.
	 *
	 * @param array $args Field arguments.
	 * @return void
	 */
	public function render_checkbox_field( $args ) {
		$checked = ! empty( $args['value'] ) ? 'checked' : '';
		?>
		<input type="checkbox" 
			   id="<?php echo esc_attr( $args['label_for'] ); ?>" 
			   name="<?php echo esc_attr( $args['name'] ); ?>" 
			   value="1" 
			   <?php echo $checked; ?>>
		<?php
		if ( isset( $args['description'] ) ) {
			echo '<p class="description">' . esc_html( $args['description'] ) . '</p>';
		}
	}

	/**
	 * Render text field.
	 *
	 * @param array $args Field arguments.
	 * @return void
	 */
	public function render_text_field( $args ) {
		?>
		<input type="text" 
			   id="<?php echo esc_attr( $args['label_for'] ); ?>" 
			   name="<?php echo esc_attr( $args['name'] ); ?>" 
			   value="<?php echo esc_attr( $args['value'] ); ?>" 
			   placeholder="<?php echo isset( $args['placeholder'] ) ? esc_attr( $args['placeholder'] ) : ''; ?>"
			   class="regular-text">
		<?php
		if ( isset( $args['description'] ) ) {
			echo '<p class="description">' . esc_html( $args['description'] ) . '</p>';
		}
	}

	/**
	 * Render textarea field.
	 *
	 * @param array $args Field arguments.
	 * @return void
	 */
	public function render_textarea_field( $args ) {
		?>
		<textarea id="<?php echo esc_attr( $args['label_for'] ); ?>" 
				  name="<?php echo esc_attr( $args['name'] ); ?>" 
				  rows="3" 
				  class="large-text"><?php echo esc_textarea( $args['value'] ); ?></textarea>
		<?php
		if ( isset( $args['description'] ) ) {
			echo '<p class="description">' . esc_html( $args['description'] ) . '</p>';
		}
	}

	/**
	 * Get plugin option value.
	 *
	 * @param string $key     Option key.
	 * @param mixed  $default Default value.
	 * @return mixed Option value.
	 */
	private function get_option( $key, $default = null ) {
		$settings = get_option( 'ru_consent_mode_settings', [] );
		return isset( $settings[ $key ] ) ? $settings[ $key ] : $default;
	}

	/**
	 * Get category map value.
	 *
	 * @param string $category Category name.
	 * @return string CSV of handles.
	 */
	private function get_category_map( $category ) {
		$settings = get_option( 'ru_consent_mode_settings', [] );
		return isset( $settings['categories_map'][ $category ] ) ? $settings['categories_map'][ $category ] : '';
	}

	/**
	 * Get content option value.
	 *
	 * @param string $lang Language code.
	 * @param string $key  Option key.
	 * @return string Option value.
	 */
	private function get_content_option( $lang, $key ) {
		$settings = get_option( 'ru_consent_mode_settings', [] );
		return isset( $settings['content'][ $lang ][ $key ] ) ? $settings['content'][ $lang ][ $key ] : '';
	}

	/**
	 * Sanitize settings.
	 *
	 * @param array $input Raw input data.
	 * @return array Sanitized data.
	 */
	public function sanitize_settings( $input ) {
		$sanitized = [];

		// Sanitize GTM settings.
		$sanitized['inject_gtm_loader'] = ! empty( $input['inject_gtm_loader'] );
		$sanitized['gtm_container_id']  = isset( $input['gtm_container_id'] ) ? sanitize_text_field( $input['gtm_container_id'] ) : '';

		// Sanitize categories map.
		if ( isset( $input['categories_map'] ) && is_array( $input['categories_map'] ) ) {
			$sanitized['categories_map'] = [];
			foreach ( [ 'analytics', 'ads', 'functional' ] as $category ) {
				if ( isset( $input['categories_map'][ $category ] ) ) {
					$sanitized['categories_map'][ $category ] = sanitize_textarea_field( $input['categories_map'][ $category ] );
				}
			}
		}

		// Sanitize content settings.
		if ( isset( $input['content'] ) && is_array( $input['content'] ) ) {
			$sanitized['content'] = [];
			foreach ( [ 'en', 'ru', 'pl' ] as $lang ) {
				if ( isset( $input['content'][ $lang ] ) ) {
					$sanitized['content'][ $lang ] = [
						'title'       => sanitize_text_field( $input['content'][ $lang ]['title'] ),
						'description' => sanitize_textarea_field( $input['content'][ $lang ]['description'] ),
						'privacy_url' => esc_url_raw( $input['content'][ $lang ]['privacy_url'] ),
					];
				}
			}
		}

		return $sanitized;
	}
}
