<?php
/**
 * Front module for Consent Mode plugin.
 *
 * Handles frontend banner display and user consent interactions.
 *
 * @package ConsentMode\Front
 */

namespace ConsentMode\Front;

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
	 * Frontend-only. Exits immediately on admin pages so banner assets do not
	 * load in wp-admin (satisfies the "Optimization" requirement of the TZ).
	 *
	 * No AJAX hooks are registered — consent is stored exclusively in the
	 * browser via the consent_preferences cookie (No-DB Policy).
	 *
	 * @return void
	 */
	public function init(): void {
		// Do not run on admin pages – assets are strictly frontend.
		if ( is_admin() ) {
			return;
		}

		// Initialize Script Guard to block tracking scripts until consent.
		ScriptGuard::instance()->init();

		// Enqueue banner CSS / JS.
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_assets' ] );

		// Render consent banner and revoke button in footer (late priority).
		add_action( 'wp_footer', [ $this, 'render_banner' ], 999 );
	}

	/**
	 * Enqueue frontend assets.
	 *
	 * @return void
	 */
	public function enqueue_assets() {
		// Enqueue banner CSS.
		wp_enqueue_style(
			'consent-mode-banner',
			CONSENT_MODE_URL . 'assets/css/banner.css',
			[],
			CONSENT_MODE_VERSION
		);

		// Enqueue banner JS.
		wp_enqueue_script(
			'consent-mode-banner',
			CONSENT_MODE_URL . 'assets/js/banner.js',
			[],
			CONSENT_MODE_VERSION,
			true
		);

		// Pass cookie config and i18n strings to JS.
		wp_localize_script(
			'consent-mode-banner',
			'consentMode',
			[
				'cookie' => [
					// Cookie name must match Consent::COOKIE_NAME.
					'name'     => \ConsentMode\Consent\Consent::COOKIE_NAME,
					'expires'  => 365,
					'path'     => COOKIEPATH,
					'domain'   => COOKIE_DOMAIN,
					'secure'   => is_ssl(),
					'sameSite' => 'Lax',
				],
				// All UI strings for the active locale, resolved server-side.
				'i18n'   => $this->get_i18n_strings(),
			]
		);
	}

	/**
	 * Render consent banner HTML.
	 *
	 * Outputs:
	 *  1. The banner strip (3-button model per GDPR best-practice).
	 *  2. A native <dialog> element for granular cookie settings.
	 *  3. The floating revoke button shown after consent is given.
	 *
	 * The banner is suppressed (only the revoke button is shown) when the
	 * consent_preferences cookie already exists (returning visitors).
	 *
	 * @return void
	 */
	public function render_banner(): void {
		$cookie_name = \ConsentMode\Consent\Consent::COOKIE_NAME;
		$has_cookie  = isset( $_COOKIE[ $cookie_name ] );

		// Revoke button — always rendered, visibility controlled by JS.
		$this->render_revocation_button();

		// Resolve locale and pick the correct i18n strings.
		$txt = $this->get_i18n_strings();

		// Privacy Policy URL: from settings first, then WP built-in, then empty.
		$privacy_url = ! empty( $txt['privacy_url'] ) ? $txt['privacy_url'] : get_privacy_policy_url();

		?>
		<!-- Consent Mode: Banner -->
		<!-- Banner is always in DOM so JS can re-show it via the revoke button. -->
		<!-- PHP adds hidden attr for returning visitors; JS removes it on revoke. -->
		<div id="ru-consent-banner"
		     class="ru-consent-banner"
		     role="dialog"
		     aria-labelledby="ru-consent-title"
		     aria-describedby="ru-consent-description"
		     aria-modal="true"
		     <?php echo $has_cookie ? 'hidden' : ''; ?>>
			<div class="ru-consent-container">
				<div class="ru-consent-content">
					<h2 id="ru-consent-title" class="ru-consent-title">
						<?php echo esc_html( $txt['title'] ); ?>
					</h2>
					<p id="ru-consent-description" class="ru-consent-description">
						<?php echo esc_html( $txt['description'] ); ?>
						<?php if ( $privacy_url ) : ?>
							<a href="<?php echo esc_url( $privacy_url ); ?>"
							   class="ru-consent-privacy-link"
							   target="_blank"
							   rel="noopener noreferrer">
								<?php echo esc_html( $txt['privacy_btn'] ); ?>
							</a>
						<?php endif; ?>
					</p>
				</div>
				<div class="ru-consent-actions">
					<!-- 3-button model per TZ: Essential | Marketing | Accept All (primary) -->
					<div class="ru-consent-btn-group">
						<button type="button" id="ru-consent-essential"
						        class="ru-consent-btn ru-consent-btn-secondary">
							<?php echo esc_html( $txt['btn_essential'] ); ?>
						</button>
						<button type="button" id="ru-consent-marketing"
						        class="ru-consent-btn ru-consent-btn-secondary">
							<?php echo esc_html( $txt['btn_marketing'] ); ?>
						</button>
						<button type="button" id="ru-consent-accept-all"
						        class="ru-consent-btn ru-consent-btn-primary">
							<?php echo esc_html( $txt['btn_accept_all'] ); ?>
						</button>
					</div>
					<button type="button" id="ru-consent-customize"
					        class="ru-consent-btn ru-consent-btn-link"
					        aria-controls="ru-consent-modal"
					        aria-expanded="false">
						<?php echo esc_html( $txt['customize'] ); ?>
					</button>
				</div>
			</div>
		</div>

		<!-- Consent Mode: Granular settings modal (native <dialog>) -->
		<dialog id="ru-consent-modal"
		        class="ru-consent-modal"
		        aria-labelledby="ru-consent-modal-title"
		        aria-modal="true">
			<div class="ru-consent-modal-inner">
				<div class="ru-consent-modal-header">
					<h3 id="ru-consent-modal-title" class="ru-consent-modal-title">
						<?php echo esc_html( $txt['modal_title'] ); ?>
					</h3>
					<button type="button" id="ru-consent-modal-close"
					        class="ru-consent-modal-close"
					        aria-label="<?php echo esc_attr( $txt['modal_close'] ); ?>">&times;</button>
				</div>
				<div class="ru-consent-modal-body">

					<!-- Necessary – always active, legitimate interest, cannot be declined -->
					<div class="ru-consent-category">
						<div class="ru-consent-category-header">
							<label class="ru-consent-category-label">
								<input type="checkbox" checked disabled
								       aria-checked="true" aria-disabled="true">
								<strong><?php echo esc_html( $txt['cat_necessary'] ); ?></strong>
							</label>
							<span class="ru-consent-badge">
								<?php echo esc_html( $txt['always_active'] ); ?>
							</span>
						</div>
						<p class="ru-consent-category-desc">
							<?php echo esc_html( $txt['cat_necessary_desc'] ); ?>
							<!-- Transparency note: disclose the technical cookie per GDPR -->
							<strong class="ru-consent-cookie-note">
								<?php
								printf(
									/* translators: %s: cookie name */
									esc_html( $txt['necessary_cookie_note'] ),
									'<code>' . esc_html( \ConsentMode\Consent\Consent::COOKIE_NAME ) . '</code>'
								);
								?>
							</strong>
						</p>
					</div>

					<!-- Statistics / Analytics -->
					<div class="ru-consent-category">
						<div class="ru-consent-category-header">
							<label class="ru-consent-category-label" for="consent-analytics">
								<input type="checkbox" id="consent-analytics" name="analytics_storage">
								<strong><?php echo esc_html( $txt['cat_analytics'] ); ?></strong>
							</label>
						</div>
						<p class="ru-consent-category-desc">
							<?php echo esc_html( $txt['cat_analytics_desc'] ); ?>
						</p>
					</div>

					<!-- Marketing / Advertising -->
					<div class="ru-consent-category">
						<div class="ru-consent-category-header">
							<label class="ru-consent-category-label" for="consent-marketing">
								<input type="checkbox" id="consent-marketing" name="ad_storage">
								<strong><?php echo esc_html( $txt['cat_marketing'] ); ?></strong>
							</label>
						</div>
						<p class="ru-consent-category-desc">
							<?php echo esc_html( $txt['cat_marketing_desc'] ); ?>
						</p>
					</div>

				</div><!-- /.ru-consent-modal-body -->
				<div class="ru-consent-modal-footer">
					<button type="button" id="ru-consent-save"
					        class="ru-consent-btn ru-consent-btn-primary">
						<?php echo esc_html( $txt['save_preferences'] ); ?>
					</button>
				</div>
			</div><!-- /.ru-consent-modal-inner -->
		</dialog>
		<?php
	}

	// -------------------------------------------------------------------------
	// Private helpers
	// -------------------------------------------------------------------------

	/**
	 * Resolve the active locale to one of the four supported language keys.
	 *
	 * Compatible with WPML and Polylang: both plugins hook into get_locale() to
	 * return the current language, so no special integration is needed.
	 *
	 * @return string One of 'en', 'ru', 'ua', 'pl'.
	 */
	private function get_locale_key(): string {
		$locale = get_locale();

		// Full-locale exact matches (higher priority).
		$exact_map = [
			'uk_UA' => 'ua',
			'ru_RU' => 'ru',
			'pl_PL' => 'pl',
		];

		if ( isset( $exact_map[ $locale ] ) ) {
			return $exact_map[ $locale ];
		}

		// Short-code fallback.
		$short_map = [
			'uk' => 'ua',
			'ru' => 'ru',
			'pl' => 'pl',
		];

		$short = substr( $locale, 0, 2 );

		return $short_map[ $short ] ?? 'en';
	}

	/**
	 * Build the complete i18n defaults array for all four supported languages.
	 *
	 * Values here are the fallbacks. Admin-configured translations (stored in
	 * consent_mode_settings[content][lang]) override these field by field.
	 *
	 * @return array<string, array<string, string>>
	 */
	private function get_i18n_defaults(): array {
		return [
			'en' => [
				'title'                => 'Cookie Consent',
				'description'          => 'We use cookies to improve your browsing experience, serve personalised ads, and analyse traffic. By clicking “Accept All” you consent to our use of cookies.',
				'privacy_btn'          => 'Privacy Policy',
				'btn_essential'        => 'Essential only',
				'btn_marketing'        => 'Marketing',
				'btn_accept_all'       => 'Accept All',
				'customize'            => 'Customize',
				'save_preferences'     => 'Save preferences',
				'modal_title'          => 'Cookie Settings',
				'modal_close'          => 'Close',
				'always_active'        => 'Always active',
				'cat_necessary'        => 'Necessary',
				'cat_necessary_desc'   => 'Required for the website to function properly.',
				'necessary_cookie_note'=> 'This website stores your choice in a technical cookie (%s) based on Legitimate Interest – this does not require your consent.',
				'cat_analytics'        => 'Statistics',
				'cat_analytics_desc'   => 'Help us understand how visitors interact with the website.',
				'cat_marketing'        => 'Marketing',
				'cat_marketing_desc'   => 'Used to deliver personalised advertisements.',
				'privacy_url'          => '',
			],
			'ru' => [
				'title'                => 'Согласие на использование файлов cookie',
				'description'          => 'Мы используем файлы cookie для улучшения работы сайта, показа персонализированной рекламы и анализа трафика. Нажимая «Принять всё», вы изъявляете согласие.',
				'privacy_btn'          => 'Политика конфиденциальности',
				'btn_essential'        => 'Только необходимые',
				'btn_marketing'        => 'Маркетинг',
				'btn_accept_all'       => 'Принять всё',
				'customize'            => 'Настроить',
				'save_preferences'     => 'Сохранить настройки',
				'modal_title'          => 'Настройки файлов cookie',
				'modal_close'          => 'Закрыть',
				'always_active'        => 'Всегда активно',
				'cat_necessary'        => 'Необходимые',
				'cat_necessary_desc'   => 'Необходимы для правильной работы сайта.',
				'necessary_cookie_note'=> 'Сайт сохраняет ваш выбор в технической куке %s. Это является «законным интересом» и не требует согласия.',
				'cat_analytics'        => 'Статистика',
				'cat_analytics_desc'   => 'Помогают нам понять, как посетители взаимодействуют с сайтом.',
				'cat_marketing'        => 'Маркетинг',
				'cat_marketing_desc'   => 'Используются для показа персонализированной рекламы.',
				'privacy_url'          => '',
			],
			'ua' => [
				'title'                => 'Згода на використання файлів cookie',
				'description'          => 'Ми використовуємо файли cookie для поліпшення роботи сайту, показу персоналізованої реклами та аналізу трафіку. Натискаючи «Прийняти все», ви надаєте згоду.',
				'privacy_btn'          => 'Політика конфіденційності',
				'btn_essential'        => 'Лише необхідні',
				'btn_marketing'        => 'Маркетинг',
				'btn_accept_all'       => 'Прийняти все',
				'customize'            => 'Налаштувати',
				'save_preferences'     => 'Зберегти налаштування',
				'modal_title'          => 'Налаштування файлів cookie',
				'modal_close'          => 'Закрити',
				'always_active'        => 'Завжди активно',
				'cat_necessary'        => 'Необхідні',
				'cat_necessary_desc'   => 'Необхідні для правильної роботи сайту.',
				'necessary_cookie_note'=> 'Сайт зберігає ваш вибір у технічному файлі cookie %s. Це є «легітимним інтересом» та не потребує згоди.',
				'cat_analytics'        => 'Статистика',
				'cat_analytics_desc'   => 'Допомагають нам зрозуміти, як відвідувачі взаємодіють із сайтом.',
				'cat_marketing'        => 'Маркетинг',
				'cat_marketing_desc'   => 'Використовуються для показу персоналізованої реклами.',
				'privacy_url'          => '',
			],
			'pl' => [
				'title'                => 'Zgoda na pliki cookie',
				'description'          => 'Używamy plików cookie, aby poprawić jakość przeglądania, wyświetlać spersonalizowane reklamy i analizować ruch. Klikając „Akceptuj wszystkie”, wyrażasz zgodę.',
				'privacy_btn'          => 'Polityka prywatności',
				'btn_essential'        => 'Tylko niezbędne',
				'btn_marketing'        => 'Marketing',
				'btn_accept_all'       => 'Akceptuj wszystkie',
				'customize'            => 'Dostosuj',
				'save_preferences'     => 'Zapisz preferencje',
				'modal_title'          => 'Ustawienia plików cookie',
				'modal_close'          => 'Zamknij',
				'always_active'        => 'Zawsze aktywne',
				'cat_necessary'        => 'Niezbędne',
				'cat_necessary_desc'   => 'Wymagane do prawidłowego działania witryny.',
				'necessary_cookie_note'=> 'Witryna zapisuje Twój wybór w technicznym pliku cookie %s zgodnie z prawnie uzasadnionym interesem – nie wymaga to Twojej zgody.',
				'cat_analytics'        => 'Statystyki',
				'cat_analytics_desc'   => 'Pomagają nam zrozumieć, w jaki sposób odwiedzający korzystają z witryny.',
				'cat_marketing'        => 'Marketing',
				'cat_marketing_desc'   => 'Używane do wyświetlania spersonalizowanych reklam.',
				'privacy_url'          => '',
			],
		];
	}

	/**
	 * Get i18n strings for the active locale, merging defaults with admin overrides.
	 *
	 * Admin-configured values (stored in wp_options) take precedence over the
	 * built-in defaults defined in get_i18n_defaults().
	 *
	 * @return array<string, string> Flat map of string keys → translated values.
	 */
	private function get_i18n_strings(): array {
		$lang     = $this->get_locale_key();
		$defaults = $this->get_i18n_defaults();
		$base     = $defaults[ $lang ] ?? $defaults['en'];

		// Load admin overrides stored as consent_mode_settings[content][lang].
		$settings  = get_option( 'consent_mode_settings', [] );
		$overrides = isset( $settings['content'][ $lang ] ) ? (array) $settings['content'][ $lang ] : [];

		// Merge: admin values override defaults, but only when non-empty.
		foreach ( $overrides as $key => $value ) {
			if ( '' !== $value && isset( $base[ $key ] ) ) {
				$base[ $key ] = $value;
			}
		}

		return $base;
	}

	/**
	 * Render revocation button.
	 *
	 * @return void
	 */
	public function render_revocation_button() {
		?>
		<button id="ru-consent-revoke"
		        class="ru-consent-revoke"
		        title="<?php echo esc_attr__( 'Cookie Settings', 'consent-mode' ); ?>"
		        aria-label="<?php echo esc_attr__( 'Cookie Settings', 'consent-mode' ); ?>"
		        hidden>
			<span class="ru-consent-revoke__icon" aria-hidden="true">🍪</span>
			<span class="ru-consent-revoke__label"><?php echo esc_html__( 'Cookie', 'consent-mode' ); ?></span>
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
