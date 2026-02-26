<?php
/**
 * Front module for RU Consent Mode plugin.
 *
 * Handles frontend banner display and user consent interactions.
 *
 * @package RUConsentMode\Front
 */

namespace RUConsentMode\Front;

/**
 * Front class.
 */
class Front {
	/**
	 * Singleton instance.
	 *
	 * @var Front|null
	 */
	private static $instance = null;

	/**
	 * Get singleton instance.
	 *
	 * @return Front
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
	 * Initialize the front module.
	 *
	 * @return void
	 */
	public function init() {
		// Initialize Script Guard for blocking tracking scripts.
		ScriptGuard::instance()->init();

		// Enqueue frontend scripts and styles.
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_assets' ] );

		// Add consent banner to footer.
		add_action( 'wp_footer', [ $this, 'render_banner' ], 999 );

		// Handle AJAX requests for consent submission.
		add_action( 'wp_ajax_ru_consent_mode_submit', [ $this, 'handle_consent_submission' ] );
		add_action( 'wp_ajax_nopriv_ru_consent_mode_submit', [ $this, 'handle_consent_submission' ] );
	}

	/**
	 * Enqueue frontend assets.
	 *
	 * @return void
	 */
	public function enqueue_assets() {
		// Enqueue banner CSS.
		wp_enqueue_style(
			'ru-consent-mode-banner',
			RU_CONSENT_MODE_URL . 'assets/css/banner.css',
			[],
			RU_CONSENT_MODE_VERSION
		);

		// Enqueue banner JS.
		wp_enqueue_script(
			'ru-consent-mode-banner',
			RU_CONSENT_MODE_URL . 'assets/js/banner.js',
			[],
			RU_CONSENT_MODE_VERSION,
			true
		);

		// Localize script with AJAX URL and nonce.
		wp_localize_script(
			'ru-consent-mode-banner',
			'ruConsentMode',
			[
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'ru_consent_mode_nonce' ),
				'cookie'  => [
					'name'     => 'ru_consent_mode',
					'expires'  => 365,
					'path'     => COOKIEPATH,
					'domain'   => COOKIE_DOMAIN,
					'secure'   => is_ssl(),
					'sameSite' => 'Lax',
				],
			]
		);
	}

	/**
	 * Render consent banner HTML.
	 *
	 * @return void
	 */
	public function render_banner() {
		// Check if user already has consent cookie.
		if ( isset( $_COOKIE['ru_consent_mode'] ) ) {
			$this->render_revocation_button();
			return;
		}

		$this->render_revocation_button(); // Render it anyway, it will be hidden by CSS/JS if banner is visible.

		// Get current language.
		$locale = get_locale();
		$lang   = substr( $locale, 0, 2 ); // 'en', 'ru', 'pl'
		if ( ! in_array( $lang, [ 'en', 'ru', 'pl' ], true ) ) {
			$lang = 'en';
		}

		// Get content from settings.
		$settings = get_option( 'ru_consent_mode_settings', [] );
		$content  = isset( $settings['content'][ $lang ] ) ? $settings['content'][ $lang ] : [];

		// Defaults.
		// Defaults based on language.
		$defaults = [
			'en' => [
				'title'                    => 'Cookie Consent',
				'description'              => 'We use cookies to enhance your browsing experience, serve personalized ads or content, and analyze our traffic. By clicking "Accept All", you consent to our use of cookies.',
				'privacy_btn'              => 'Privacy Policy',
				'accept_all'               => 'Accept All',
				'reject_all'               => 'Reject All',
				'customize'                => 'Customize',
				'save_preferences'         => 'Save Preferences',
				'category_necessary'       => 'Necessary',
				'category_necessary_desc'  => 'Required for the website to function properly',
				'category_analytics'       => 'Analytics',
				'category_analytics_desc'  => 'Helps us understand how visitors interact with the website',
				'category_ads'             => 'Advertising',
				'category_ads_desc'        => 'Used to deliver personalized ads',
				'category_functional'      => 'Functional',
				'category_functional_desc' => 'Enables enhanced functionality like videos and maps',
			],
			'ru' => [
				'title'                    => 'Согласие на использование файлов cookie',
				'description'              => 'Мы используем файлы cookie для улучшения работы сайта, показа персонализированной рекламы и анализа трафика. Нажимая «Принять все», вы даете согласие на использование файлов cookie.',
				'privacy_btn'              => 'Политика конфиденциальности',
				'accept_all'               => 'Принять все',
				'reject_all'               => 'Отклонить все',
				'customize'                => 'Настроить',
				'save_preferences'         => 'Сохранить настройки',
				'category_necessary'       => 'Необходимые',
				'category_necessary_desc'  => 'Необходимы для правильной работы сайта',
				'category_analytics'       => 'Аналитика',
				'category_analytics_desc'  => 'Помогают нам понять, как посетители взаимодействуют с сайтом',
				'category_ads'             => 'Реклама',
				'category_ads_desc'        => 'Используются для показа персонализированной рекламы',
				'category_functional'      => 'Функциональные',
				'category_functional_desc' => 'Включают расширенные функции, такие как видео и карты',
			],
			'pl' => [
				'title'                    => 'Zgoda na pliki cookie',
				'description'              => 'Używamy plików cookie, aby poprawić jakość przeglądania, wyświetlać spersonalizowane reklamy lub treści oraz analizować nasz ruch. Klikając „Akceptuj wszystkie”, wyrażasz zgodę na używanie przez nas plików cookie.',
				'privacy_btn'              => 'Polityka prywatności',
				'accept_all'               => 'Akceptuj wszystkie',
				'reject_all'               => 'Odrzuć wszystkie',
				'customize'                => 'Dostosuj',
				'save_preferences'         => 'Zapisz ustawienia',
				'category_necessary'       => 'Niezbędne',
				'category_necessary_desc'  => 'Wymagane do prawidłowego działania witryny',
				'category_analytics'       => 'Analityka',
				'category_analytics_desc'  => 'Pomagają nam zrozumieć, w jaki sposób odwiedzający korzystają z witryny',
				'category_ads'             => 'Reklama',
				'category_ads_desc'        => 'Używane do wyświetlania spersonalizowanych reklam',
				'category_functional'      => 'Funkcjonalne',
				'category_functional_desc' => 'Umożliwiają rozszerzone funkcje, takie jak wideo i mapy',
			],
		];

		$default_content = isset( $defaults[ $lang ] ) ? $defaults[ $lang ] : $defaults['en'];

		$title       = ! empty( $content['title'] ) ? $content['title'] : $default_content['title'];
		$description = ! empty( $content['description'] ) ? $content['description'] : $default_content['description'];
		$privacy_url = ! empty( $content['privacy_url'] ) ? $content['privacy_url'] : '';
		
		// Helper to get text (customizable in future, currently hardcoded defaults)
		$txt = $default_content;

		?>
		<div id="ru-consent-banner" class="ru-consent-banner" role="dialog" aria-labelledby="ru-consent-title" aria-describedby="ru-consent-description">
			<div class="ru-consent-container">
				<div class="ru-consent-content">
					<h2 id="ru-consent-title" class="ru-consent-title">
						<?php echo esc_html( $title ); ?>
					</h2>
					<p id="ru-consent-description" class="ru-consent-description">
						<?php echo esc_html( $description ); ?>
						<?php if ( $privacy_url ) : ?>
							<a href="<?php echo esc_url( $privacy_url ); ?>" target="_blank"><?php echo esc_html( $txt['privacy_btn'] ); ?></a>
						<?php endif; ?>
					</p>
					<div class="ru-consent-categories" style="display: none;">
						<div class="ru-consent-category">
							<label>
								<input type="checkbox" name="security_storage" checked disabled>
								<strong><?php echo esc_html( $txt['category_necessary'] ); ?></strong>
								<span class="ru-consent-category-desc"><?php echo esc_html( $txt['category_necessary_desc'] ); ?></span>
							</label>
						</div>
						<div class="ru-consent-category">
							<label>
								<input type="checkbox" name="analytics_storage" id="consent-analytics">
								<strong><?php echo esc_html( $txt['category_analytics'] ); ?></strong>
								<span class="ru-consent-category-desc"><?php echo esc_html( $txt['category_analytics_desc'] ); ?></span>
							</label>
						</div>
						<div class="ru-consent-category">
							<label>
								<input type="checkbox" name="ad_storage" id="consent-ads">
								<strong><?php echo esc_html( $txt['category_ads'] ); ?></strong>
								<span class="ru-consent-category-desc"><?php echo esc_html( $txt['category_ads_desc'] ); ?></span>
							</label>
						</div>
						<div class="ru-consent-category">
							<label>
								<input type="checkbox" name="functionality_storage" id="consent-functional">
								<strong><?php echo esc_html( $txt['category_functional'] ); ?></strong>
								<span class="ru-consent-category-desc"><?php echo esc_html( $txt['category_functional_desc'] ); ?></span>
							</label>
						</div>
					</div>
				</div>
				<div class="ru-consent-actions">
					<button type="button" class="ru-consent-btn ru-consent-btn-primary" id="ru-consent-accept-all">
						<?php echo esc_html( $txt['accept_all'] ); ?>
					</button>
					<button type="button" class="ru-consent-btn ru-consent-btn-secondary" id="ru-consent-reject-all">
						<?php echo esc_html( $txt['reject_all'] ); ?>
					</button>
					<button type="button" class="ru-consent-btn ru-consent-btn-link" id="ru-consent-customize">
						<?php echo esc_html( $txt['customize'] ); ?>
					</button>
					<button type="button" class="ru-consent-btn ru-consent-btn-primary" id="ru-consent-save" style="display: none;">
						<?php echo esc_html( $txt['save_preferences'] ); ?>
					</button>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Handle consent submission via AJAX.
	 *
	 * @return void
	 */
	public function handle_consent_submission() {
		// Verify nonce.
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'ru_consent_mode_nonce' ) ) {
			wp_send_json_error( [ 'message' => __( 'Invalid nonce', 'ru-consent-mode' ) ] );
		}

		// Get consent data.
		$consent_data = isset( $_POST['consent'] ) ? json_decode( wp_unslash( $_POST['consent'] ), true ) : [];

		if ( ! is_array( $consent_data ) ) {
			wp_send_json_error( [ 'message' => __( 'Invalid consent data', 'ru-consent-mode' ) ] );
		}

		// Sanitize consent data.
		$sanitized_consent = [
			'analytics_storage'     => isset( $consent_data['analytics_storage'] ) && 'granted' === $consent_data['analytics_storage'] ? 'granted' : 'denied',
			'ad_storage'            => isset( $consent_data['ad_storage'] ) && 'granted' === $consent_data['ad_storage'] ? 'granted' : 'denied',
			'ad_user_data'          => isset( $consent_data['ad_user_data'] ) && 'granted' === $consent_data['ad_user_data'] ? 'granted' : 'denied',
			'ad_personalization'    => isset( $consent_data['ad_personalization'] ) && 'granted' === $consent_data['ad_personalization'] ? 'granted' : 'denied',
			'functionality_storage' => isset( $consent_data['functionality_storage'] ) && 'granted' === $consent_data['functionality_storage'] ? 'granted' : 'denied',
			'personalization_storage' => isset( $consent_data['personalization_storage'] ) && 'granted' === $consent_data['personalization_storage'] ? 'granted' : 'denied',
			'security_storage'      => 'granted', // Always granted.
		];

		// TODO: Log consent to database.
		// Consent\Consent::instance()->log_consent( $sanitized_consent );

		// Return success response.
		wp_send_json_success( [
			'message' => __( 'Consent saved successfully', 'ru-consent-mode' ),
			'consent' => $sanitized_consent,
		] );
	}

	/**
	 * Render revocation button.
	 *
	 * @return void
	 */
	public function render_revocation_button() {
		?>
		<button id="ru-consent-revoke" class="ru-consent-revoke" title="<?php echo esc_attr__( 'Cookie Settings', 'ru-consent-mode' ); ?>" style="display: none;">
			<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
				<path d="M12 2a10 10 0 1 0 10 10 4 4 0 0 1-5-5 4 4 0 0 1-5-5"></path>
				<path d="M8.5 8.5v.01"></path>
				<path d="M16 15.5v.01"></path>
				<path d="M12 12v.01"></path>
				<path d="M11 17v.01"></path>
				<path d="M7 14v.01"></path>
			</svg>
		</button>
		<?php
	}

	/**
	 * Inject Google Consent Mode script.
	 *
	 * @return void
	 */
	public function inject_gcm_script() {
		// TODO: Output default consent state before any tags load.
		// TODO: Configure consent parameters based on user location.
	}
}
