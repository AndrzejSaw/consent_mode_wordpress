<?php
/**
 * Admin module for Consent Mode plugin.
 *
 * Handles admin panel functionality, settings pages, and backend operations.
 *
 * @package ConsentMode\Admin
 */

namespace ConsentMode\Admin;

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

		// Enqueue admin styles and scripts (tabs JS for language manager).
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_assets' ] );

		// TODO: Add settings link to plugins page.
		// add_filter( 'plugin_action_links_' . CONSENT_MODE_BASENAME, [ $this, 'add_settings_link' ] );

		// TODO: Handle AJAX requests for admin operations.
		// add_action( 'wp_ajax_consent_mode_save_settings', [ $this, 'save_settings' ] );
	}

	/**
	 * Register admin menu pages.
	 *
	 * @return void
	 */
	public function register_menu() {
		add_options_page(
			__( 'Consent Mode Settings', 'consent-mode' ),
			__( 'Consent Mode', 'consent-mode' ),
			'manage_options',
			'consent-mode-settings',
			[ $this, 'render_settings_page' ]
		);
	}

	/**
	 * Register plugin settings.
	 *
	 * Only GTM and Script Guard fields use add_settings_field().
	 * The multilingual content section is rendered entirely by
	 * render_content_section() to allow a tabbed UI.
	 *
	 * @return void
	 */
	public function register_settings() {
		// Register main settings array (single wp_options row).
		register_setting(
			'consent_mode_settings_group',
			'consent_mode_settings',
			[ $this, 'sanitize_settings' ]
		);

		// ---- GTM Section ----
		add_settings_section(
			'consent_mode_gtm_section',
			__( 'Google Tag Manager Settings', 'consent-mode' ),
			[ $this, 'render_gtm_section' ],
			'consent-mode-settings'
		);

		add_settings_field(
			'inject_gtm_loader',
			__( 'Enable GTM Loader', 'consent-mode' ),
			[ $this, 'render_checkbox_field' ],
			'consent-mode-settings',
			'consent_mode_gtm_section',
			[
				'label_for' => 'inject_gtm_loader',
				'name'      => 'consent_mode_settings[inject_gtm_loader]',
				'value'     => $this->get_option( 'inject_gtm_loader', false ),
			]
		);

		add_settings_field(
			'gtm_container_id',
			__( 'GTM Container ID', 'consent-mode' ),
			[ $this, 'render_text_field' ],
			'consent-mode-settings',
			'consent_mode_gtm_section',
			[
				'label_for'   => 'gtm_container_id',
				'name'        => 'consent_mode_settings[gtm_container_id]',
				'value'       => $this->get_option( 'gtm_container_id', '' ),
				'placeholder' => 'GTM-XXXXXXX',
			]
		);

		// ---- Script Guard Section ----
		add_settings_section(
			'consent_mode_script_guard_section',
			__( 'Script Guard Settings', 'consent-mode' ),
			[ $this, 'render_script_guard_section' ],
			'consent-mode-settings'
		);

		add_settings_field(
			'categories_map_analytics',
			__( 'Analytics Scripts', 'consent-mode' ),
			[ $this, 'render_textarea_field' ],
			'consent-mode-settings',
			'consent_mode_script_guard_section',
			[
				'label_for'   => 'categories_map_analytics',
				'name'        => 'consent_mode_settings[categories_map][analytics]',
				'value'       => $this->get_category_map( 'analytics' ),
				'description' => __( 'Comma-separated script handles to block until analytics consent. E.g.: ga4, clarity, matomo', 'consent-mode' ),
			]
		);

		add_settings_field(
			'categories_map_ads',
			__( 'Marketing Scripts', 'consent-mode' ),
			[ $this, 'render_textarea_field' ],
			'consent-mode-settings',
			'consent_mode_script_guard_section',
			[
				'label_for'   => 'categories_map_ads',
				'name'        => 'consent_mode_settings[categories_map][ads]',
				'value'       => $this->get_category_map( 'ads' ),
				'description' => __( 'Comma-separated script handles to block until marketing consent. E.g.: googletag, fb-pixel, adsbygoogle', 'consent-mode' ),
			]
		);

		add_settings_field(
			'categories_map_functional',
			__( 'Functional Scripts', 'consent-mode' ),
			[ $this, 'render_textarea_field' ],
			'consent-mode-settings',
			'consent_mode_script_guard_section',
			[
				'label_for'   => 'categories_map_functional',
				'name'        => 'consent_mode_settings[categories_map][functional]',
				'value'       => $this->get_category_map( 'functional' ),
				'description' => __( 'Comma-separated script handles always active (e.g. embeds, maps). E.g.: youtube, vimeo, maps', 'consent-mode' ),
			]
		);

		// Multilingual Content Section.
		// NOTE: Fields are rendered manually inside render_content_section() using
		// a tabbed UI. We do NOT use add_settings_field() for content fields so
		// we have full control over the HTML structure.
		add_settings_section(
			'consent_mode_content_section',
			__( 'Multilingual Banner Content', 'consent-mode' ),
			[ $this, 'render_content_section' ],
			'consent-mode-settings'
		);
	}

	/**
	 * Render settings page.
	 *
	 * @return void
	 */
	public function render_settings_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'consent-mode' ) );
		}
		?>
		<div class="wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
			<form method="post" action="options.php">
				<?php
				settings_fields( 'consent_mode_settings_group' );
				do_settings_sections( 'consent-mode-settings' );
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
		echo '<p>' . esc_html__( 'Configure Google Tag Manager integration.', 'consent-mode' ) . '</p>';
	}

	/**
	 * Render Script Guard section description.
	 *
	 * @return void
	 */
	public function render_script_guard_section() {
		echo '<p>' . esc_html__( 'Configure which scripts should be blocked until user consent is granted. Enter script handles as comma-separated values.', 'consent-mode' ) . '</p>';
		echo '<p><strong>' . esc_html__( 'Priority Order:', 'consent-mode' ) . '</strong> ' . esc_html__( 'If a script handle appears in multiple categories, the priority is: Ads > Analytics > Functional', 'consent-mode' ) . '</p>';
	}

	/**
	 * Render Content section — full tabbed Language Manager.
	 *
	 * Outputs tab navigation for EN / RU / UA / PL and all i18n fields for each
	 * language. Fields follow the naming convention:
	 *   consent_mode_settings[content][lang][field_key]
	 * so they are handled automatically by sanitize_settings().
	 *
	 * @return void
	 */
	public function render_content_section(): void {
		echo '<p>' . esc_html__( 'Customise the banner text for each supported language. Leave fields empty to use built-in defaults.', 'consent-mode' ) . '</p>';

		// Language tabs definition: key => label.
		$languages = [
			'en' => __( 'English', 'consent-mode' ),
			'ru' => __( 'Russian', 'consent-mode' ),
			'ua' => __( 'Ukrainian', 'consent-mode' ),
			'pl' => __( 'Polish', 'consent-mode' ),
		];

		// Fields definition: key => [label, type (text|textarea|url)].
		$fields = [
			'title'                 => [ __( 'Banner title', 'consent-mode' ), 'text' ],
			'description'           => [ __( 'Banner description text', 'consent-mode' ), 'textarea' ],
			'privacy_url'           => [ __( 'Privacy Policy URL', 'consent-mode' ), 'url' ],
			'btn_essential'         => [ __( 'Button: Essential only', 'consent-mode' ), 'text' ],
			'btn_marketing'         => [ __( 'Button: Marketing', 'consent-mode' ), 'text' ],
			'btn_accept_all'        => [ __( 'Button: Accept All (primary)', 'consent-mode' ), 'text' ],
			'customize'             => [ __( 'Link: Customize', 'consent-mode' ), 'text' ],
			'save_preferences'      => [ __( 'Button: Save preferences (modal)', 'consent-mode' ), 'text' ],
			'modal_title'           => [ __( 'Modal title', 'consent-mode' ), 'text' ],
			'cat_necessary_desc'    => [ __( 'Category: Necessary (description)', 'consent-mode' ), 'textarea' ],
			'cat_analytics'         => [ __( 'Category: Statistics (label)', 'consent-mode' ), 'text' ],
			'cat_analytics_desc'    => [ __( 'Category: Statistics (description)', 'consent-mode' ), 'textarea' ],
			'cat_marketing'         => [ __( 'Category: Marketing (label)', 'consent-mode' ), 'text' ],
			'cat_marketing_desc'    => [ __( 'Category: Marketing (description)', 'consent-mode' ), 'textarea' ],
		];

		$first_lang = array_key_first( $languages );
		?>
		<div class="rcm-lang-tabs" id="rcm-lang-tabs">
			<!-- Tab navigation -->
			<nav class="rcm-lang-tabs__nav" role="tablist" aria-label="<?php esc_attr_e( 'Language tabs', 'consent-mode' ); ?>">
				<?php foreach ( $languages as $lang_code => $lang_label ) : ?>
					<button type="button"
					        class="rcm-lang-tab <?php echo $lang_code === $first_lang ? 'rcm-lang-tab--active' : ''; ?>"
					        role="tab"
					        id="rcm-tab-<?php echo esc_attr( $lang_code ); ?>"
					        aria-controls="rcm-panel-<?php echo esc_attr( $lang_code ); ?>"
					        aria-selected="<?php echo $lang_code === $first_lang ? 'true' : 'false'; ?>"
					        data-lang="<?php echo esc_attr( $lang_code ); ?>">
						<?php echo esc_html( $lang_label ); ?>
					</button>
				<?php endforeach; ?>
			</nav>

			<!-- Tab panels -->
			<?php foreach ( $languages as $lang_code => $lang_label ) : ?>
				<div class="rcm-lang-panel <?php echo $lang_code !== $first_lang ? 'rcm-lang-panel--hidden' : ''; ?>"
				     id="rcm-panel-<?php echo esc_attr( $lang_code ); ?>"
				     role="tabpanel"
				     aria-labelledby="rcm-tab-<?php echo esc_attr( $lang_code ); ?>">
					<table class="form-table" role="presentation">
						<tbody>
						<?php foreach ( $fields as $field_key => [ $field_label, $field_type ] ) : ?>
							<tr>
								<th scope="row">
									<label for="rcm-<?php echo esc_attr( "{$lang_code}-{$field_key}" ); ?>">
										<?php echo esc_html( $field_label ); ?>
									</label>
								</th>
								<td>
								<?php
								$field_name  = "consent_mode_settings[content][{$lang_code}][{$field_key}]";
								$field_id    = "rcm-{$lang_code}-{$field_key}";
								$field_value = $this->get_content_option( $lang_code, $field_key );

								if ( 'textarea' === $field_type ) :
									?>
									<textarea id="<?php echo esc_attr( $field_id ); ?>"
									          name="<?php echo esc_attr( $field_name ); ?>"
									          rows="3"
									          class="large-text"><?php echo esc_textarea( $field_value ); ?></textarea>
									<?php
								elseif ( 'url' === $field_type ) :
									?>
									<input type="url"
									       id="<?php echo esc_attr( $field_id ); ?>"
									       name="<?php echo esc_attr( $field_name ); ?>"
									       value="<?php echo esc_attr( $field_value ); ?>"
									       class="regular-text"
									       placeholder="https://example.com/privacy-policy">
									<?php
								else :
									?>
									<input type="text"
									       id="<?php echo esc_attr( $field_id ); ?>"
									       name="<?php echo esc_attr( $field_name ); ?>"
									       value="<?php echo esc_attr( $field_value ); ?>"
									       class="regular-text">
									<?php
								endif;
								?>
								</td>
							</tr>
						<?php endforeach; ?>
						</tbody>
					</table>
				</div><!-- /.rcm-lang-panel -->
			<?php endforeach; ?>
		</div><!-- /.rcm-lang-tabs -->

		<style>
			.rcm-lang-tabs__nav { display: flex; gap: 4px; margin-bottom: 0; border-bottom: 1px solid #c3c4c7; }
			.rcm-lang-tab { padding: 6px 14px; border: 1px solid #c3c4c7; border-bottom: none; background: #f6f7f7; cursor: pointer; border-radius: 3px 3px 0 0; font-size: 13px; }
			.rcm-lang-tab--active { background: #fff; font-weight: 600; border-bottom: 1px solid #fff; margin-bottom: -1px; }
			.rcm-lang-panel { border: 1px solid #c3c4c7; border-top: none; background: #fff; padding: 0 10px; }
			.rcm-lang-panel--hidden { display: none; }
		</style>
		<script>
		(function() {
			document.querySelectorAll('.rcm-lang-tab').forEach(function(btn) {
				btn.addEventListener('click', function() {
					var lang = this.dataset.lang;
					document.querySelectorAll('.rcm-lang-tab').forEach(function(b) {
						b.classList.remove('rcm-lang-tab--active');
						b.setAttribute('aria-selected', 'false');
					});
					document.querySelectorAll('.rcm-lang-panel').forEach(function(p) {
						p.classList.add('rcm-lang-panel--hidden');
					});
					this.classList.add('rcm-lang-tab--active');
					this.setAttribute('aria-selected', 'true');
					document.getElementById('rcm-panel-' + lang).classList.remove('rcm-lang-panel--hidden');
				});
			});
		}());
		</script>
		<?php
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
		$settings = get_option( 'consent_mode_settings', [] );
		return isset( $settings[ $key ] ) ? $settings[ $key ] : $default;
	}

	/**
	 * Get category map value.
	 *
	 * @param string $category Category name.
	 * @return string CSV of handles.
	 */
	private function get_category_map( $category ) {
		$settings = get_option( 'consent_mode_settings', [] );
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
		$settings = get_option( 'consent_mode_settings', [] );
		return isset( $settings['content'][ $lang ][ $key ] ) ? $settings['content'][ $lang ][ $key ] : '';
	}

	/**
	 * Enqueue admin styles and scripts.
	 *
	 * Loaded only on the plugin settings page to avoid polluting other screens.
	 *
	 * @param string $hook_suffix Current admin page hook suffix.
	 * @return void
	 */
	public function enqueue_admin_assets( string $hook_suffix ): void {
		// Only load on our settings page.
		if ( 'settings_page_consent-mode-settings' !== $hook_suffix ) {
			return;
		}
		// Tabs JS is inlined directly in render_content_section() — no external
		// file needed. This method is a stable hook point for future additions.
	}

	/**
	 * Sanitize settings before saving to wp_options.
	 *
	 * Handles all four languages (EN, RU, UA, PL) and all i18n field keys.
	 * Unknown fields are dropped; URLs are sanitised with esc_url_raw().
	 *
	 * @param array $input Raw input data from the settings form.
	 * @return array Sanitized settings array.
	 */
	public function sanitize_settings( $input ): array {
		$sanitized = [];

		// GTM settings.
		$sanitized['inject_gtm_loader'] = ! empty( $input['inject_gtm_loader'] );
		$sanitized['gtm_container_id']  = isset( $input['gtm_container_id'] )
			? sanitize_text_field( $input['gtm_container_id'] )
			: '';

		// Script Guard categories map.
		if ( isset( $input['categories_map'] ) && is_array( $input['categories_map'] ) ) {
			$sanitized['categories_map'] = [];
			foreach ( [ 'analytics', 'ads', 'functional' ] as $category ) {
				$sanitized['categories_map'][ $category ] = isset( $input['categories_map'][ $category ] )
					? sanitize_textarea_field( $input['categories_map'][ $category ] )
					: '';
			}
		}

		// Multilingual content – 4 languages × all i18n field keys.
		$languages = [ 'en', 'ru', 'ua', 'pl' ];

		// Text fields (single-line).
		$text_fields = [
			'title', 'privacy_btn', 'btn_essential', 'btn_marketing', 'btn_accept_all',
			'customize', 'save_preferences', 'modal_title', 'modal_close', 'always_active',
			'cat_necessary', 'cat_analytics', 'cat_marketing',
		];

		// Textarea fields (multi-line).
		$textarea_fields = [
			'description', 'cat_necessary_desc', 'necessary_cookie_note',
			'cat_analytics_desc', 'cat_marketing_desc',
		];

		if ( isset( $input['content'] ) && is_array( $input['content'] ) ) {
			$sanitized['content'] = [];

			foreach ( $languages as $lang ) {
				if ( ! isset( $input['content'][ $lang ] ) || ! is_array( $input['content'][ $lang ] ) ) {
					continue;
				}

				$lang_input = $input['content'][ $lang ];
				$sanitized_lang = [];

				foreach ( $text_fields as $field ) {
					if ( isset( $lang_input[ $field ] ) ) {
						$sanitized_lang[ $field ] = sanitize_text_field( $lang_input[ $field ] );
					}
				}

				foreach ( $textarea_fields as $field ) {
					if ( isset( $lang_input[ $field ] ) ) {
						$sanitized_lang[ $field ] = sanitize_textarea_field( $lang_input[ $field ] );
					}
				}

				// Privacy URL: special handling.
				if ( isset( $lang_input['privacy_url'] ) ) {
					$sanitized_lang['privacy_url'] = esc_url_raw( $lang_input['privacy_url'] );
				}

				$sanitized['content'][ $lang ] = $sanitized_lang;
			}
		}

		return $sanitized;
	}
}
