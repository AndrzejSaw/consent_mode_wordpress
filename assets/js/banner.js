/**
 * Consent Banner â€“ Consent Mode plugin.
 *
 * Handles the consent banner UI, GCMv2 gtag() updates, script
 * reactivation, and cookie storage without any server-side AJAX calls
 * (No-DB / stateless architecture).
 *
 * Requires: banner HTML rendered by Front::render_banner() and
 *           consentMode config object injected via wp_localize_script().
 *
 * ES6+ â€“ no build step required. Strict mode throughout.
 *
 * @package ConsentMode
 */

( function () {
	'use strict';

	// -----------------------------------------------------------------------
	// Config â€“ resolved from wp_localize_script() output (consentMode).
	// -----------------------------------------------------------------------

	/**
	 * Name of the consent cookie.
	 * Synced with Consent::COOKIE_NAME via wp_localize_script.
	 *
	 * @type {string}
	 */
	const COOKIE_NAME = window.consentMode?.cookie?.name ?? 'consent_preferences';

	// -----------------------------------------------------------------------
	// Consent data factories
	// -----------------------------------------------------------------------

	/**
	 * Build a "all granted" consent object (Accept All / Marketing buttons).
	 *
	 * Per TZ: both "Marketing" and "Accept All" buttons grant identical
	 * consent â€“ they differ only in visual weight (secondary vs primary).
	 *
	 * @returns {Object}
	 */
	function buildGrantedAll() {
		return {
			ad_storage:               'granted',
			ad_user_data:             'granted',
			ad_personalization:       'granted',
			analytics_storage:        'granted',
			functionality_storage:    'granted',
			personalization_storage:  'granted',
			security_storage:         'granted',
		};
	}

	/**
	 * Build an "essential only" consent object (Strict Privacy / Essential button).
	 *
	 * @returns {Object}
	 */
	function buildEssentialOnly() {
		return {
			ad_storage:               'denied',
			ad_user_data:             'denied',
			ad_personalization:       'denied',
			analytics_storage:        'denied',
			functionality_storage:    'granted', // Legitimate Interest â€“ site functionality.
			personalization_storage:  'denied',
			security_storage:         'granted', // Legitimate Interest â€“ security.
		};
	}

	/**
	 * Build a granular consent object from modal checkbox state.
	 *
	 * @returns {Object}
	 */
	function buildGranular() {
		const analyticsGranted = document.getElementById( 'consent-analytics' )?.checked ?? false;
		const marketingGranted = document.getElementById( 'consent-marketing' )?.checked ?? false;

		return {
			ad_storage:               marketingGranted ? 'granted' : 'denied',
			ad_user_data:             marketingGranted ? 'granted' : 'denied',
			ad_personalization:       marketingGranted ? 'granted' : 'denied',
			analytics_storage:        analyticsGranted ? 'granted' : 'denied',
			functionality_storage:    'granted',
			personalization_storage:  'denied',
			security_storage:         'granted',
		};
	}

	// -----------------------------------------------------------------------
	// GCMv2 â€“ gtag() integration
	// -----------------------------------------------------------------------

	/**
	 * Push a consent update to the dataLayer via gtag().
	 *
	 * Safe to call before GTM boots â€“ dataLayer is initialised in Bootstrap.php
	 * at wp_head priority 0 which always fires before any script.
	 *
	 * @param {Object} consentData GCMv2 consent parameter map.
	 * @returns {void}
	 */
	function pushConsentUpdate( consentData ) {
		if ( typeof window.gtag === 'function' ) {
			window.gtag( 'consent', 'update', consentData );
		}
	}

	// -----------------------------------------------------------------------
	// Cookie helpers (SameSite=Lax; Secure when HTTPS)
	// -----------------------------------------------------------------------

	/**
	 * Persist consent data in the consent_preferences cookie.
	 *
	 * Cookie attributes are taken from consentMode.cookie config injected
	 * by wp_localize_script() so they match server-side settings exactly.
	 *
	 * @param {Object} consentData
	 * @returns {void}
	 */
	function saveConsentCookie( consentData ) {
		const cfg  = window.consentMode?.cookie ?? {};
		const days = cfg.expires ?? 365;

		const expires = new Date();
		expires.setTime( expires.getTime() + days * 24 * 60 * 60 * 1000 );

		const path     = cfg.path    ? `;path=${ cfg.path }` : ';path=/';
		const domain   = cfg.domain  ? `;domain=${ cfg.domain }` : '';
		const secure   = cfg.secure  ? ';Secure'                  : '';
		const sameSite = `;SameSite=${ cfg.sameSite ?? 'Lax' }`;

		document.cookie = [
			`${ COOKIE_NAME }=${ encodeURIComponent( JSON.stringify( consentData ) ) }`,
			`expires=${ expires.toUTCString() }`,
			path, domain, secure, sameSite,
		].join( '' );
	}

	/**
	 * Read and parse the consent cookie.
	 *
	 * @returns {Object|null} Parsed consent object, or null if absent / invalid.
	 */
	function readConsentCookie() {
		const prefix = COOKIE_NAME + '=';
		const parts  = document.cookie.split( ';' );

		for ( let i = 0; i < parts.length; i++ ) {
			const part = parts[ i ].trimStart();
			if ( part.startsWith( prefix ) ) {
				try {
					return JSON.parse( decodeURIComponent( part.slice( prefix.length ) ) );
				} catch {
					return null;
				}
			}
		}

		return null;
	}

	// -----------------------------------------------------------------------
	// Script reactivation (ScriptGuard integration)
	// -----------------------------------------------------------------------

	/**
	 * Reactivate all blocked scripts for a consent category.
	 *
	 * Supports both attribute conventions:
	 *  - data-rcm-consent="analytics"   (internal, legacy)
	 *  - data-consent-category="analytics" (public-facing spec per TZ)
	 *
	 * @param {string} category analytics|ads|functional
	 * @returns {void}
	 */
	function reactivateByCategory( category ) {
		const selector = [
			`script[type="text/plain"][data-rcm-consent="${ category }"]`,
			`script[type="text/plain"][data-consent-category="${ category }"]`,
		].join( ', ' );

		const placeholders = document.querySelectorAll( selector );

		if ( ! placeholders.length ) {
			return;
		}

		placeholders.forEach( reactivateScript );
	}

	/**
	 * Reactivate a single blocked-script placeholder element.
	 *
	 * @param {HTMLElement} placeholder <script type="text/plain"> element.
	 * @returns {void}
	 */
	function reactivateScript( placeholder ) {
		const src = placeholder.getAttribute( 'data-src' );

		if ( src ) {
			reactivateExternalScript( placeholder, src );
		} else {
			reactivateInlineScript( placeholder );
		}
	}

	/**
	 * Reactivate an external (src=) script.
	 *
	 * Restores all original attributes preserved by ScriptGuard.php.
	 *
	 * @param {HTMLElement} placeholder
	 * @param {string} src
	 * @returns {void}
	 */
	function reactivateExternalScript( placeholder, src ) {
		const script = document.createElement( 'script' );
		script.src   = src;

		const originalType = placeholder.getAttribute( 'data-original-type' );
		if ( originalType ) { script.type = originalType; }
		if ( placeholder.hasAttribute( 'data-async' ) ) { script.async = true; }
		if ( placeholder.hasAttribute( 'data-defer' ) ) { script.defer = true; }

		const crossorigin = placeholder.getAttribute( 'data-crossorigin' );
		if ( crossorigin ) { script.crossOrigin = crossorigin; }

		const integrity = placeholder.getAttribute( 'data-integrity' );
		if ( integrity ) { script.integrity = integrity; }

		const nonce = placeholder.getAttribute( 'data-nonce' );
		if ( nonce ) { script.nonce = nonce; }

		// Preserve unknown data-* attributes (not internal data-rcm-* / data-consent-* ones).
		const skipPrefixes = [ 'data-rcm-', 'data-consent-', 'data-src', 'data-async',
			'data-defer', 'data-crossorigin', 'data-integrity', 'data-nonce', 'data-original-type' ];

		Array.from( placeholder.attributes ).forEach( ( attr ) => {
			if ( attr.name.startsWith( 'data-' ) && ! skipPrefixes.some( p => attr.name.startsWith( p ) ) ) {
				script.setAttribute( attr.name, attr.value );
			}
		} );

		placeholder.parentNode?.replaceChild( script, placeholder );
	}

	/**
	 * Reactivate an inline script.
	 *
	 * @param {HTMLElement} placeholder
	 * @returns {void}
	 */
	function reactivateInlineScript( placeholder ) {
		const script = document.createElement( 'script' );
		const originalType = placeholder.getAttribute( 'data-original-type' );
		script.type        = originalType ?? 'text/javascript';
		script.textContent  = placeholder.textContent;
		placeholder.parentNode?.replaceChild( script, placeholder );
	}

	// -----------------------------------------------------------------------
	// Core consent flow
	// -----------------------------------------------------------------------

	/**
	 * Central consent update: push GCMv2, save cookie, unblock scripts,
	 * hide the banner, and dispatch a custom event.
	 *
	 * No AJAX calls â€“ fully stateless as required by the No-DB Policy.
	 *
	 * @param {Object} consentData
	 * @returns {void}
	 */
	function applyConsent( consentData ) {
		// 1. Update Google Consent Mode via gtag().
		pushConsentUpdate( consentData );

		// 2. Persist choice in browser cookie (SameSite=Lax; Secure on HTTPS).
		saveConsentCookie( consentData );

		// 3. Unblock scripts that just received consent.
		if ( consentData.analytics_storage === 'granted' ) {
			reactivateByCategory( 'analytics' );
		}
		if ( consentData.ad_storage === 'granted' ) {
			reactivateByCategory( 'ads' );
		}
		// Functional scripts are always reactivated (Legitimate Interest).
		reactivateByCategory( 'functional' );

		// 4. Close modal if open and hide banner.
		closeModal();
		hideBanner();

		// 5. Notify other scripts (theme integrations etc.).
		document.dispatchEvent( new CustomEvent( 'ruConsentUpdated', { detail: consentData } ) );
	}

	// -----------------------------------------------------------------------
	// Banner visibility
	// -----------------------------------------------------------------------

	/**
	 * Animate-out and hide the consent banner; show revoke button.
	 *
	 * @returns {void}
	 */
	function hideBanner() {
		const banner    = document.getElementById( 'ru-consent-banner' );
		const revokeBtn = document.getElementById( 'ru-consent-revoke' );

		if ( ! banner ) { return; }

		banner.classList.add( 'ru-consent-banner--hiding' );

		setTimeout( () => {
			banner.hidden = true;
			banner.classList.remove( 'ru-consent-banner--hiding' );
			if ( revokeBtn ) { revokeBtn.hidden = false; }
		}, 300 );
	}

	/**
	 * Show the consent banner and hide the revoke button.
	 *
	 * @returns {void}
	 */
	function showBanner() {
		const banner    = document.getElementById( 'ru-consent-banner' );
		const revokeBtn = document.getElementById( 'ru-consent-revoke' );

		if ( banner ) { banner.hidden = false; }
		if ( revokeBtn ) { revokeBtn.hidden = true; }

		// Move initial focus to the first action button for accessibility.
		const firstBtn = banner?.querySelector( '.ru-consent-btn' );
		firstBtn?.focus();
	}

	// -----------------------------------------------------------------------
	// Modal (native <dialog>)
	// -----------------------------------------------------------------------

	/**
	 * Open the granular settings modal.
	 *
	 * Uses the native HTMLDialogElement.showModal() which:
	 *  - Creates an implicit backdrop (::backdrop pseudo-element)
	 *  - Traps focus within the dialog automatically
	 *  - Fires 'cancel' on Escape key
	 *
	 * @returns {void}
	 */
	function openModal() {
		const modal = document.getElementById( 'ru-consent-modal' );
		if ( ! modal ) { return; }

		// Pre-fill checkboxes from existing cookie if present.
		const existing = readConsentCookie();
		if ( existing ) {
			const analyticsCheck = document.getElementById( 'consent-analytics' );
			const marketingCheck = document.getElementById( 'consent-marketing' );
			if ( analyticsCheck ) { analyticsCheck.checked = existing.analytics_storage === 'granted'; }
			if ( marketingCheck ) { marketingCheck.checked = existing.ad_storage          === 'granted'; }
		}

		// Update aria-expanded on the customize button.
		const customizeBtn = document.getElementById( 'ru-consent-customize' );
		if ( customizeBtn ) { customizeBtn.setAttribute( 'aria-expanded', 'true' ); }

		modal.showModal();
	}

	/**
	 * Close the granular settings modal.
	 *
	 * @returns {void}
	 */
	function closeModal() {
		const modal = document.getElementById( 'ru-consent-modal' );
		if ( modal?.open ) {
			modal.close();
		}

		const customizeBtn = document.getElementById( 'ru-consent-customize' );
		if ( customizeBtn ) { customizeBtn.setAttribute( 'aria-expanded', 'false' ); }
	}

	// -----------------------------------------------------------------------
	// Event handlers (button clicks, modal close)
	// -----------------------------------------------------------------------

	/** Accept All â€“ all granted, primary CTA. */
	function handleAccept() {
		applyConsent( buildGrantedAll() );
	}

	/**
	 * Marketing â€“ all granted, secondary button.
	 *
	 * Per TZ spec: "ĐśĐ°Ń€ĐşĐµŃ‚Đ¸Đ˝Đł: ĐŁŃŃ‚Đ°Đ˝Đ°Đ˛Đ»Đ¸Đ˛Đ°ĐµŃ‚ ŃŃ€ĐľĐ˛ĐµĐ˝ŃŚ granted Đ´Đ»ŃŹ Đ˛ŃĐµŃ…
	 * ĐżĐ°Ń€Đ°ĐĽĐµŃ‚Ń€ĐľĐ˛ GCMv2. ĐĐ˝Đ°Đ»ĐľĐłĐ¸Ń‡Đ˝Đľ ĐźŃ€Đ¸Đ˝ŃŹŃ‚ŃŚ Đ˛ŃŃ‘, Đ˝Đľ secondary button."
	 */
	function handleMarketing() {
		applyConsent( buildGrantedAll() );
	}

	/** Essential only â€“ all optional denied. */
	function handleEssential() {
		applyConsent( buildEssentialOnly() );
	}

	/** Customize â€“ open granular modal. */
	function handleCustomize() {
		openModal();
	}

	/** Save from modal â€“ reads checkboxes. */
	function handleSave() {
		applyConsent( buildGranular() );
	}

	// -----------------------------------------------------------------------
	// Event listener setup
	// -----------------------------------------------------------------------

	/**
	 * Attach click / dialog event listeners.
	 *
	 * All elements are checked for existence before use to silence
	 * ReferenceErrors on pages where the banner is intentionally absent.
	 *
	 * @returns {void}
	 */
	function attachEventListeners() {
		const banner = document.getElementById( 'ru-consent-banner' );
		if ( ! banner ) { return; }

		// ---- Banner buttons ------------------------------------------------
		document.getElementById( 'ru-consent-accept-all' )
			?.addEventListener( 'click', handleAccept );

		document.getElementById( 'ru-consent-marketing' )
			?.addEventListener( 'click', handleMarketing );

		document.getElementById( 'ru-consent-essential' )
			?.addEventListener( 'click', handleEssential );

		document.getElementById( 'ru-consent-customize' )
			?.addEventListener( 'click', handleCustomize );

		// ---- Modal controls ------------------------------------------------
		// NOTE: saveBtn is declared here â€“ fixing the ReferenceError that
		// existed in the previous version where it was used before declaration.
		const saveBtn = document.getElementById( 'ru-consent-save' );
		saveBtn?.addEventListener( 'click', handleSave );

		document.getElementById( 'ru-consent-modal-close' )
			?.addEventListener( 'click', closeModal );

		// Close modal when user clicks the native <dialog> backdrop area.
		const modal = document.getElementById( 'ru-consent-modal' );
		modal?.addEventListener( 'click', ( event ) => {
			// dialog element fills the viewport; a click directly on it (not its
			// children) means the backdrop was clicked.
			if ( event.target === modal ) {
				closeModal();
			}
		} );

		// Native <dialog> fires 'cancel' on Escape â€“ close gracefully.
		modal?.addEventListener( 'cancel', ( event ) => {
			event.preventDefault(); // Prevent the dialog closing without calling closeModal.
			closeModal();
		} );

		// ---- Revoke button -------------------------------------------------
		document.getElementById( 'ru-consent-revoke' )
			?.addEventListener( 'click', showBanner );
	}

	// -----------------------------------------------------------------------
	// Initialisation
	// -----------------------------------------------------------------------

	/**
	 * Re-apply consent from existing cookie for returning visitors.
	 *
	 * Unblocks scripts that match the stored consent state without
	 * showing the banner again.
	 *
	 * @returns {void}
	 */
	function checkExistingConsent() {
		const consentData = readConsentCookie();
		if ( ! consentData ) { return; }

		// Silently push the stored consent to GCMv2 (session restore).
		pushConsentUpdate( consentData );

		if ( consentData.analytics_storage === 'granted' ) {
			reactivateByCategory( 'analytics' );
		}
		if ( consentData.ad_storage === 'granted' ) {
			reactivateByCategory( 'ads' );
		}
		reactivateByCategory( 'functional' );
	}

	/**
	 * Show the revoke button when the banner is already dismissed.
	 *
	 * @returns {void}
	 */
	function checkRevocationButton() {
		const consentData = readConsentCookie();
		const banner      = document.getElementById( 'ru-consent-banner' );
		const revokeBtn   = document.getElementById( 'ru-consent-revoke' );

		if ( ! revokeBtn ) { return; }

		if ( consentData && ( ! banner || banner.hidden ) ) {
			revokeBtn.hidden = false;
		}
	}

	/**
	 * Main entry point.
	 *
	 * @returns {void}
	 */
	function init() {
		attachEventListeners();
		checkExistingConsent();
		checkRevocationButton();
	}

	// Run on DOM ready.
	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}

	// Expose to global scope for external integrations.
	window.RUConsentBanner = {
		init,
		showBanner,
		hideBanner,
		openModal,
		closeModal,
		applyConsent,
		readConsentCookie,
	};

}() );
