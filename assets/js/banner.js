/**
 * Consent Banner JavaScript for RU Consent Mode plugin.
 *
 * @package RUConsentMode
 */

(function() {
	'use strict';

	/**
	 * RU Consent Mode Banner Handler
	 */
	const RUConsentBanner = {
		/**
		 * Initialize the banner
		 */
		init() {
			this.attachEventListeners();
			
			// Check for existing consent and reactivate scripts if needed
			this.checkExistingConsent();

			// Check if revocation button should be shown
			this.checkRevocationButton();
		},

		/**
		 * Check if revocation button should be shown
		 */
		checkRevocationButton() {
			const consent = this.getCookie('ru_consent_mode');
			const banner = document.getElementById('ru-consent-banner');
			const revokeBtn = document.getElementById('ru-consent-revoke');

			if (consent && (!banner || banner.style.display === 'none')) {
				if (revokeBtn) {
					revokeBtn.style.display = 'flex';
				}
			}
		},

		/**
		 * Check for existing consent and reactivate scripts
		 */
		checkExistingConsent() {
			const consent = this.getCookie('ru_consent_mode');
			
			if (!consent) {
				return;
			}

			try {
				const consentData = JSON.parse(consent);
				
				// Reactivate scripts based on consent
				if (consentData.analytics_storage === 'granted') {
					this.reactivateByCategory('analytics');
				}
				
				if (consentData.ad_storage === 'granted') {
					this.reactivateByCategory('ads');
				}
				
				// Functional scripts are always reactivated
				this.reactivateByCategory('functional');
				
			} catch (error) {
				console.error('Error parsing consent cookie:', error);
			}
		},

		/**
		 * Attach event listeners to banner buttons
		 */
		attachEventListeners() {
			const banner = document.getElementById('ru-consent-banner');
			if (!banner) return;

			// Accept all button
			const acceptBtn = document.getElementById('ru-consent-accept-all');
			if (acceptBtn) {
				acceptBtn.addEventListener('click', () => this.handleAccept());
			}

			// Reject all button
			const rejectBtn = document.getElementById('ru-consent-reject-all');
			if (rejectBtn) {
				rejectBtn.addEventListener('click', () => this.handleReject());
			}

			// Customize button
			const customizeBtn = document.getElementById('ru-consent-customize');
			if (customizeBtn) {
				customizeBtn.addEventListener('click', () => this.handleCustomize());
			}

			if (saveBtn) {
				saveBtn.addEventListener('click', () => this.handleSave());
			}

			// Revoke button
			const revokeBtn = document.getElementById('ru-consent-revoke');
			if (revokeBtn) {
				revokeBtn.addEventListener('click', () => this.showBanner());
			}
		},

		/**
		 * Handle accept button click
		 */
		handleAccept() {
			const consentData = {
				ad_storage: 'granted',
				ad_user_data: 'granted',
				ad_personalization: 'granted',
				analytics_storage: 'granted',
				functionality_storage: 'granted',
				personalization_storage: 'granted',
				security_storage: 'granted'
			};

			this.updateConsent(consentData);
		},

		/**
		 * Handle reject button click
		 */
		handleReject() {
			const consentData = {
				ad_storage: 'denied',
				ad_user_data: 'denied',
				ad_personalization: 'denied',
				analytics_storage: 'denied',
				functionality_storage: 'granted', // Necessary
				personalization_storage: 'denied',
				security_storage: 'granted' // Necessary
			};

			this.updateConsent(consentData);
		},

		/**
		 * Handle customize button click
		 */
		handleCustomize() {
			// Show the categories section
			const categoriesSection = document.querySelector('.ru-consent-categories');
			if (categoriesSection) {
				categoriesSection.style.display = 'block';
			}

			// Hide default buttons
			const acceptBtn = document.getElementById('ru-consent-accept-all');
			const rejectBtn = document.getElementById('ru-consent-reject-all');
			const customizeBtn = document.getElementById('ru-consent-customize');
			if (acceptBtn) acceptBtn.style.display = 'none';
			if (rejectBtn) rejectBtn.style.display = 'none';
			if (customizeBtn) customizeBtn.style.display = 'none';

			// Show save button
			const saveBtn = document.getElementById('ru-consent-save');
			if (saveBtn) saveBtn.style.display = 'inline-block';
		},

		/**
		 * Handle save preferences button click
		 */
		handleSave() {
			// Get checkbox values
			const analyticsChecked = document.getElementById('consent-analytics')?.checked || false;
			const adsChecked = document.getElementById('consent-ads')?.checked || false;
			const functionalChecked = document.getElementById('consent-functional')?.checked || false;

			const consentData = {
				ad_storage: adsChecked ? 'granted' : 'denied',
				ad_user_data: adsChecked ? 'granted' : 'denied',
				ad_personalization: adsChecked ? 'granted' : 'denied',
				analytics_storage: analyticsChecked ? 'granted' : 'denied',
				functionality_storage: functionalChecked ? 'granted' : 'denied',
				personalization_storage: functionalChecked ? 'granted' : 'denied',
				security_storage: 'granted' // Always granted
			};

			this.updateConsent(consentData);
		},

		/**
		 * Update consent state
		 *
		 * @param {Object} consentData - Consent data object
		 */
		updateConsent(consentData) {
			// Update Google Consent Mode via gtag
			if (typeof gtag === 'function') {
				gtag('consent', 'update', consentData);
			}

			// Save to cookie with proper expiration
			this.setCookie('ru_consent_mode', JSON.stringify(consentData), 365);

			// Send to backend via AJAX for logging
			this.sendConsentToBackend(consentData);

			// Reactivate scripts based on consent
			if (consentData.analytics_storage === 'granted') {
				this.reactivateByCategory('analytics');
			}

			if (consentData.ad_storage === 'granted') {
				this.reactivateByCategory('ads');
			}

			// Functional scripts are always reactivated
			this.reactivateByCategory('functional');

			// Hide banner
			this.hideBanner();

			// Trigger custom event for other scripts
			document.dispatchEvent(new CustomEvent('ruConsentUpdated', {
				detail: consentData
			}));
		},

		/**
		 * Reactivate blocked scripts by consent category
		 *
		 * @param {string} category - Consent category (analytics|ads|functional)
		 */
		reactivateByCategory(category) {
			// Find all placeholder scripts for this category
			const placeholders = document.querySelectorAll(
				`script[type="text/plain"][data-rcm-consent="${category}"]`
			);

			if (!placeholders.length) {
				console.log(`[RU Consent Mode] No scripts found for category: ${category}`);
				return;
			}

			console.log(`[RU Consent Mode] Reactivating ${placeholders.length} script(s) for category: ${category}`);

			// Reactivate each placeholder
			placeholders.forEach(placeholder => this.reactivateScript(placeholder));
		},

		/**
		 * Reactivate a single script placeholder
		 *
		 * @param {HTMLElement} placeholder - Placeholder script element
		 */
		reactivateScript(placeholder) {
			const src = placeholder.getAttribute('data-src');

			if (src) {
				// External script
				this.reactivateExternalScript(placeholder, src);
			} else {
				// Inline script
				this.reactivateInlineScript(placeholder);
			}
		},

		/**
		 * Reactivate external script
		 *
		 * @param {HTMLElement} placeholder - Placeholder script element
		 * @param {string} src - Script source URL
		 */
		reactivateExternalScript(placeholder, src) {
			// Create new script element
			const script = document.createElement('script');
			script.src = src;

			// Restore original type (if not default)
			const originalType = placeholder.getAttribute('data-original-type');
			if (originalType) {
				script.type = originalType;
			}

			// Restore async attribute
			if (placeholder.hasAttribute('data-async')) {
				script.async = true;
			}

			// Restore defer attribute
			if (placeholder.hasAttribute('data-defer')) {
				script.defer = true;
			}

			// Restore crossorigin attribute
			const crossorigin = placeholder.getAttribute('data-crossorigin');
			if (crossorigin) {
				script.crossOrigin = crossorigin;
			}

			// Restore integrity attribute
			const integrity = placeholder.getAttribute('data-integrity');
			if (integrity) {
				script.integrity = integrity;
			}

			// Restore nonce attribute
			const nonce = placeholder.getAttribute('data-nonce');
			if (nonce) {
				script.nonce = nonce;
			}

			// Copy other data attributes
			Array.from(placeholder.attributes).forEach(attr => {
				if (attr.name.startsWith('data-') && 
					!attr.name.startsWith('data-rcm-') && 
					!attr.name.startsWith('data-src') &&
					!attr.name.startsWith('data-async') &&
					!attr.name.startsWith('data-defer') &&
					!attr.name.startsWith('data-crossorigin') &&
					!attr.name.startsWith('data-integrity') &&
					!attr.name.startsWith('data-nonce') &&
					!attr.name.startsWith('data-original-type')) {
					script.setAttribute(attr.name, attr.value);
				}
			});

			script.onload = () => {
				console.log(`[RU Consent Mode] Script loaded: ${src}`);
			};

			script.onerror = () => {
				console.error(`[RU Consent Mode] Failed to load script: ${src}`);
			};

			// Replace placeholder with active script
			placeholder.parentNode.replaceChild(script, placeholder);
		},

		/**
		 * Reactivate inline script
		 *
		 * @param {HTMLElement} placeholder - Placeholder script element
		 */
		reactivateInlineScript(placeholder) {
			// Create new script element
			const script = document.createElement('script');

			// Restore original type (if not default)
			const originalType = placeholder.getAttribute('data-original-type');
			if (originalType) {
				script.type = originalType;
			} else {
				script.type = 'text/javascript';
			}

			// Copy inline content
			script.textContent = placeholder.textContent;

			// Replace placeholder with active script
			placeholder.parentNode.replaceChild(script, placeholder);

			console.log('[RU Consent Mode] Inline script reactivated');
		},

		/**
		 * Send consent data to backend
		 *
		 * @param {Object} consentData - Consent data object
		 */
		sendConsentToBackend(consentData) {
			const ajaxUrl = window.ruConsentMode?.ajaxUrl || '/wp-admin/admin-ajax.php';
			const nonce = window.ruConsentMode?.nonce || '';

			fetch(ajaxUrl, {
				method: 'POST',
				headers: {
					'Content-Type': 'application/x-www-form-urlencoded',
				},
				body: new URLSearchParams({
					action: 'ru_consent_mode_submit',
					nonce: nonce,
					consent: JSON.stringify(consentData)
				})
			})
			.then(response => response.json())
			.then(data => {
				if (data.success) {
					console.log('[RU Consent Mode] Consent saved successfully:', data);
				} else {
					console.error('[RU Consent Mode] Error saving consent:', data);
				}
			})
			.catch(error => {
				console.error('[RU Consent Mode] AJAX error:', error);
			});
		},

		/**
		 * Hide the consent banner
		 */
		hideBanner() {
			const banner = document.getElementById('ru-consent-banner');
			if (banner) {
				banner.classList.add('ru-consent-banner--hidden');
				setTimeout(() => {
					banner.style.display = 'none';
					banner.classList.remove('ru-consent-banner--hidden'); // Reset class for next show
					
					// Show revocation button
					const revokeBtn = document.getElementById('ru-consent-revoke');
					if (revokeBtn) {
						revokeBtn.style.display = 'flex';
					}
				}, 300);
			}
		},

		/**
		 * Show the consent banner
		 */
		showBanner() {
			const banner = document.getElementById('ru-consent-banner');
			const revokeBtn = document.getElementById('ru-consent-revoke');

			if (banner) {
				banner.style.display = 'block';
				// Hide revocation button
				if (revokeBtn) {
					revokeBtn.style.display = 'none';
				}
			}
		},

		/**
		 * Set a cookie
		 *
		 * @param {string} name - Cookie name
		 * @param {string} value - Cookie value
		 * @param {number} days - Expiration in days
		 */
		setCookie(name, value, days) {
			const date = new Date();
			date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
			const expires = "expires=" + date.toUTCString();
			
			// Use config from localized script if available
			const config = window.ruConsentMode?.cookie || {};
			const path = config.path || '/';
			const domain = config.domain ? `;domain=${config.domain}` : '';
			const secure = config.secure ? ';Secure' : '';
			const sameSite = config.sameSite || 'Lax';
			
			document.cookie = `${name}=${value};${expires};path=${path}${domain}${secure};SameSite=${sameSite}`;
		},

		/**
		 * Get a cookie
		 *
		 * @param {string} name - Cookie name
		 * @return {string|null} Cookie value or null
		 */
		getCookie(name) {
			const nameEQ = name + "=";
			const ca = document.cookie.split(';');
			for (let i = 0; i < ca.length; i++) {
				let c = ca[i];
				while (c.charAt(0) === ' ') c = c.substring(1, c.length);
				if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length, c.length);
			}
			return null;
		}
	};

	// Initialize on DOM ready
	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', () => RUConsentBanner.init());
	} else {
		RUConsentBanner.init();
	}

	// Expose to global scope if needed
	window.RUConsentBanner = RUConsentBanner;

})();
