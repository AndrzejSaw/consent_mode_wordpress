<?php
/**
 * Bootstrap module for Consent Mode plugin.
 *
 * Handles Google Consent Mode v2 initialization and GTM integration.
 *
 * @package ConsentMode\Consent
 */

namespace ConsentMode\Consent;

/**
 * Bootstrap class for consent mode initialization.
 */
class Bootstrap {
	/**
	 * Singleton instance.
	 *
	 * @var Bootstrap|null
	 */
	private static $instance = null;

	/**
	 * Plugin settings.
	 *
	 * @var array
	 */
	private $settings = [];

	/**
	 * Get singleton instance.
	 *
	 * @return Bootstrap
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
		$this->settings = get_option( 'consent_mode_settings', [] );
	}

	/**
	 * Initialize the bootstrap module.
	 *
	 * @return void
	 */
	public function init() {
		// Hook 1: Initialize dataLayer and default consent state (priority 0 - earliest)
		add_action( 'wp_head', [ $this, 'inject_consent_mode_default' ], 0 );

		// Hook 2: Inject GTM loader if enabled (priority 5)
		add_action( 'wp_head', [ $this, 'inject_gtm_loader' ], 5 );

		// Hook 3: GTM noscript iframe in body
		add_action( 'wp_body_open', [ $this, 'inject_gtm_noscript' ], 0 );
	}

	/**
	 * Inject Google Consent Mode default state.
	 *
	 * Creates dataLayer, gtag stub, and sets default consent values.
	 * Priority 0 ensures this runs before any other scripts.
	 *
	 * @return void
	 */
	public function inject_consent_mode_default() {
		// Get default consent values based on user's location.
		$default_consent = $this->get_default_consent_state();

		// Check if ads data redaction is enabled.
		$ads_data_redaction = $this->get_setting( 'ads_data_redaction', true );

		// Check if URL passthrough is enabled.
		$url_passthrough = $this->get_setting( 'url_passthrough', false );

		// Get wait time for consent update.
		$wait_for_update = absint( $this->get_setting( 'wait_for_update', 500 ) );

		?>
<!-- Consent Mode (GCMv2) - Default Consent State -->
<script data-consent-mode="default">
(function() {
	'use strict';
	
	// Initialize dataLayer if it doesn't exist (avoid duplication).
	window.dataLayer = window.dataLayer || [];
	
	/**
	 * gtag function stub for consent mode.
	 * Pushes arguments to dataLayer.
	 */
	function gtag() {
		dataLayer.push(arguments);
	}
	
	// Make gtag available globally if not already defined.
	if (typeof window.gtag === 'undefined') {
		window.gtag = gtag;
	}
	
	// Set default consent state (all denied for privacy-first approach).
	gtag('consent', 'default', <?php echo wp_json_encode( $default_consent, JSON_UNESCAPED_SLASHES ); ?>);
	
	<?php if ( $ads_data_redaction ) : ?>
	// Enable ads data redaction for enhanced privacy.
	gtag('set', 'ads_data_redaction', true);
	<?php endif; ?>
	
	<?php if ( $url_passthrough ) : ?>
	// Enable URL passthrough for cross-domain consent.
	gtag('set', 'url_passthrough', true);
	<?php endif; ?>
	
	<?php if ( $wait_for_update > 0 ) : ?>
	// Set wait time for consent update (milliseconds).
	gtag('set', 'wait_for_update', <?php echo esc_js( $wait_for_update ); ?>);
	<?php endif; ?>
	
	// Log consent mode initialization (development only).
	if (window.console && typeof console.log === 'function') {
		console.log('[Consent Mode] Default consent state initialized:', <?php echo wp_json_encode( $default_consent ); ?>);
	}
})();
</script>
<!-- End Consent Mode - Default Consent State -->
		<?php
	}

	/**
	 * Inject Google Tag Manager loader script.
	 *
	 * Only injects if GTM is enabled and container ID is set.
	 * Priority 5 ensures this runs after consent mode initialization.
	 *
	 * @return void
	 */
	public function inject_gtm_loader() {
		// Check if GTM injection is enabled.
		$inject_gtm_loader = $this->get_setting( 'inject_gtm_loader', false );
		
		if ( ! $inject_gtm_loader ) {
			return;
		}

		// Get GTM container ID.
		$gtm_container_id = $this->get_setting( 'gtm_container_id', '' );
		
		// Validate container ID format (GTM-XXXXXXX).
		if ( empty( $gtm_container_id ) || ! $this->validate_gtm_id( $gtm_container_id ) ) {
			// Log error in debug mode.
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				error_log( '[Consent Mode] Invalid or missing GTM container ID: ' . $gtm_container_id );
			}
			return;
		}

		// Sanitize container ID.
		$gtm_id = esc_js( $gtm_container_id );

		// Get additional GTM parameters.
		$gtm_auth       = $this->get_setting( 'gtm_auth', '' );
		$gtm_preview    = $this->get_setting( 'gtm_preview', '' );
		$gtm_cookies_win = $this->get_setting( 'gtm_cookies_win', '' );

		// Build GTM URL parameters.
		$gtm_params = $this->build_gtm_params( $gtm_auth, $gtm_preview, $gtm_cookies_win );

		?>
<!-- Google Tag Manager (Consent Mode) -->
<script data-gtm-id="<?php echo esc_attr( $gtm_id ); ?>">
(function(w,d,s,l,i){
	// Prevent duplicate GTM injection.
	if (w.google_tag_manager && w.google_tag_manager[i]) {
		console.warn('[Consent Mode] GTM container already loaded:', i);
		return;
	}
	
	w[l]=w[l]||[];
	w[l].push({'gtm.start': new Date().getTime(), event:'gtm.js'});
	
	var f=d.getElementsByTagName(s)[0],
		j=d.createElement(s),
		dl=l!='dataLayer'?'&l='+l:'';
	
	j.async=true;
	j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl<?php echo $gtm_params; ?>;
	
	// Add error handling for GTM script loading.
	j.onerror = function() {
		console.error('[Consent Mode] Failed to load GTM container:', i);
	};
	
	f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','<?php echo $gtm_id; ?>');
</script>
<!-- End Google Tag Manager -->
		<?php
	}

	/**
	 * Inject GTM noscript iframe.
	 *
	 * Fallback for users with JavaScript disabled.
	 * Uses wp_body_open hook (WordPress 5.2+).
	 *
	 * @return void
	 */
	public function inject_gtm_noscript() {
		// Check if GTM injection is enabled.
		$inject_gtm_loader = $this->get_setting( 'inject_gtm_loader', false );
		
		if ( ! $inject_gtm_loader ) {
			return;
		}

		// Get GTM container ID.
		$gtm_container_id = $this->get_setting( 'gtm_container_id', '' );
		
		// Validate container ID.
		if ( empty( $gtm_container_id ) || ! $this->validate_gtm_id( $gtm_container_id ) ) {
			return;
		}

		// Sanitize container ID.
		$gtm_id = esc_attr( $gtm_container_id );

		// Get additional GTM parameters.
		$gtm_auth       = $this->get_setting( 'gtm_auth', '' );
		$gtm_preview    = $this->get_setting( 'gtm_preview', '' );
		$gtm_cookies_win = $this->get_setting( 'gtm_cookies_win', '' );

		// Build GTM URL parameters for iframe.
		$gtm_params = $this->build_gtm_params( $gtm_auth, $gtm_preview, $gtm_cookies_win, true );

		?>
<!-- Google Tag Manager (noscript) -->
<noscript>
	<iframe src="https://www.googletagmanager.com/ns.html?id=<?php echo $gtm_id . $gtm_params; ?>" 
			height="0" 
			width="0" 
			style="display:none;visibility:hidden"
			title="Google Tag Manager"
			aria-hidden="true">
	</iframe>
</noscript>
<!-- End Google Tag Manager (noscript) -->
		<?php
	}

	/**
	 * Get default consent state based on geolocation.
	 *
	 * @return array Default consent configuration.
	 */
	private function get_default_consent_state() {
		// Get user's country code from Geo module.
		$geo          = \ConsentMode\Geo\Geo::instance();
		$country_code = $geo->get_country_code();

		// Determine if user is in a region requiring strict consent (EEA, PL, UA, BY, RU).
		$is_strict_region = $geo->is_strict_mode();

		// Default consent state (privacy-first approach).
		$default_consent = [
			'ad_storage'              => 'denied',
			'ad_user_data'            => 'denied',
			'ad_personalization'      => 'denied',
			'analytics_storage'       => 'denied',
			'functionality_storage'   => 'granted', // Always granted for site functionality.
			'personalization_storage' => 'denied',
			'security_storage'        => 'granted', // Always granted for security.
		];

		// Allow customization via filter.
		$default_consent = apply_filters(
			'consent_mode_default_consent',
			$default_consent,
			$country_code,
			$is_strict_region
		);

		// Add region-specific behavior.
		if ( $is_strict_region ) {
			$default_consent['region'] = [ $country_code ];
		}

		return $default_consent;
	}

	/**
	 * Build GTM URL parameters.
	 *
	 * @param string $auth        GTM auth parameter.
	 * @param string $preview     GTM preview parameter.
	 * @param string $cookies_win GTM cookies_win parameter.
	 * @param bool   $for_iframe  Whether building params for iframe (use & instead of &amp;).
	 * @return string URL parameters string.
	 */
	private function build_gtm_params( $auth = '', $preview = '', $cookies_win = '', $for_iframe = false ) {
		$params = '';

		// Add auth parameter.
		if ( ! empty( $auth ) ) {
			$separator = $for_iframe ? '&' : '&amp;';
			$params   .= $separator . 'gtm_auth=' . esc_attr( $auth );
		}

		// Add preview parameter.
		if ( ! empty( $preview ) ) {
			$separator = $for_iframe ? '&' : '&amp;';
			$params   .= $separator . 'gtm_preview=' . esc_attr( $preview );
		}

		// Add cookies_win parameter.
		if ( ! empty( $cookies_win ) ) {
			$separator = $for_iframe ? '&' : '&amp;';
			$params   .= $separator . 'gtm_cookies_win=' . esc_attr( $cookies_win );
		}

		// For script tag, wrap in quotes.
		if ( ! $for_iframe && ! empty( $params ) ) {
			$params = "+'$params'";
		}

		return $params;
	}

	/**
	 * Validate GTM container ID format.
	 *
	 * @param string $gtm_id GTM container ID to validate.
	 * @return bool True if valid format.
	 */
	private function validate_gtm_id( $gtm_id ) {
		// GTM ID format: GTM-XXXXXXX (where X is alphanumeric).
		return (bool) preg_match( '/^GTM-[A-Z0-9]+$/i', $gtm_id );
	}

	/**
	 * Get plugin setting with default fallback.
	 *
	 * @param string $key     Setting key.
	 * @param mixed  $default Default value.
	 * @return mixed Setting value or default.
	 */
	private function get_setting( $key, $default = null ) {
		return isset( $this->settings[ $key ] ) ? $this->settings[ $key ] : $default;
	}

	/**
	 * Check if dataLayer already exists.
	 *
	 * This is a server-side check - actual check happens in JavaScript.
	 *
	 * @return bool Always returns false (client-side check is authoritative).
	 */
	private function has_existing_datalayer() {
		// TODO: Implement server-side detection if needed.
		// For now, rely on JavaScript check in inject_consent_mode_default().
		return false;
	}
}
