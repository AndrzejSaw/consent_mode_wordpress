=== Universal Consent Mode (GCMv2) ===
Contributors: yourname
Tags: gdpr, consent, google consent mode, privacy, gcm v2, cookie banner, multilingual, rodo, pke, pke 2024
Requires at least: 6.2
Tested up to: 6.7
Requires PHP: 8.1
Stable tag: 1.1.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Universal WordPress plugin for Google Consent Mode v2 with multilingual support (EN, RU, UA, PL), stateless No-DB architecture and strict GDPR/PKE 2024 compliance.

== Description ==

Universal Consent Mode (GCMv2) is a privacy-first WordPress plugin implementing Google Consent Mode v2. It is designed for websites serving EU/EEA visitors and fully complies with GDPR and the Polish Electronic Communications Act (PKE, Dz. U. 2024 poz. 1221) of 12 July 2024.

**No database tables.** User consent is stored exclusively in a browser cookie (`consent_preferences`) â€” true Privacy by Design as required by GDPR Art. 25.

= Key Features =

* **3-button consent model** (Essential Only / Marketing / Accept All) with Customize modal
* **Native `<dialog>` element** for granular category preferences â€” no libraries required
* **No-DB stateless architecture** â€” consent stored in cookie only, zero SQL tables
* **Google Consent Mode v2** â€” full 7-parameter support with immediate `gtag update`
* **Google Tag Manager** â€” optional auto-loading with staging environment support
* **Script Guard** â€” blocks analytics/ads/functional scripts until consent; auto-reactivation
* **4 languages**: EN, RU, UA, PL â€” automatic locale detection, WPML/Polylang compatible
* **Admin Language Manager** â€” edit all banner and modal texts per language
* **Permanent revocation button** â€” accessible on every page (GDPR Art. 7)
* **Geolocation** â€” Cloudflare headers + WordPress timezone fallback, strict EU/EEA mode
* **WCAG accessibility** â€” ARIA roles, focus trap, keyboard navigation

= Legal Compliance =

* GDPR (EU) 2016/679 â€” Art. 5, 6, 7, 25
* PKE 2024 (Polish Electronic Communications Act) â€” Art. 399, 400
* Google Consent Mode v2 â€” all 7 parameters
* EDPB guidelines on cookie consent

= Supported GCMv2 Parameters =

`ad_storage`, `ad_user_data`, `ad_personalization`, `analytics_storage`,
`functionality_storage`, `personalization_storage`, `security_storage`

== Installation ==

1. Upload the `consent-mode` folder to `/wp-content/plugins/`
2. Activate the plugin in **Plugins** menu
3. Go to **Settings â†’ Consent Mode Settings**
4. (Optional) Enter your GTM Container ID and enable GTM loader
5. Add WordPress script handles to Script Guard categories
6. Customize banner text in the Language Manager tab

**Note:** The main plugin file is `consent-mode.php`. The old `ru-consent-mode.php`
(v1.0.x) is deprecated â€” deactivate it and activate the new file.

== Frequently Asked Questions ==

= What is Google Consent Mode v2? =

Google Consent Mode v2 is Google's framework for managing user consent signals for cookies and data collection. It passes consent status to Google tags via `ad_storage`, `analytics_storage`, `ad_user_data`, `ad_personalization` and other parameters.

= Is this plugin No-DB? =

Yes. User consent preferences are stored exclusively in the `consent_preferences` browser cookie. The plugin uses WordPress `wp_options` only for plugin settings (standard Settings API). No custom database tables are created.

= Which languages are supported? =

English (en), Russian (ru), Ukrainian (ua), Polish (pl). The language is detected automatically from the WordPress site locale. Compatible with WPML and Polylang.

= Is this plugin compatible with the Polish PKE 2024? =

Yes. The plugin implements opt-in for all optional cookie categories (analytics, marketing) as required by Art. 400 PKE. Strictly necessary cookies remain active without consent under Art. 399 PKE.

= How do I revoke consent? =

After consent is given and the banner is hidden, a permanent cookie settings button appears at the bottom of every page. Clicking it re-opens the banner and allows changing or withdrawing consent at any time.

= Does the plugin work without Google Tag Manager? =

Yes. GTM loading is optional. The plugin always initialises Google Consent Mode v2 defaults regardless of whether GTM is used.

== Screenshots ==

1. Consent banner â€” 3-button model with Privacy Policy link
2. Granular preferences modal (native dialog)
3. Admin settings page â€” Language Manager tabs
4. Admin settings page â€” Script Guard configuration

== Changelog ==

= 1.1.0 â€” 2026-02-26 =
* Added Ukrainian (UA) language support
* Added native `<dialog>` modal for granular consent settings
* Implemented 3-button consent model (Essential / Marketing / Accept All)
* Changed plugin main file to `consent-mode.php` (renamed from `ru-consent-mode.php`)
* Changed PHP namespace to `ConsentMode\` (from `RUConsentMode\`)
* Changed consent cookie name to `consent_preferences`
* Removed all AJAX calls â€” fully stateless architecture
* Removed database tables from Log module
* Fixed: `saveBtn` ReferenceError in banner.js
* Fixed: double loading of assets in wp-admin
* Added permanent revocation button with `hidden` attribute

= 1.0.0 â€” 2025-10-23 =
* Initial release
* Google Consent Mode v2 initialization
* Google Tag Manager integration
* Script Guard (analytics, ads, functional categories)
* Multilingual support (EN, RU, PL)
* Admin settings panel

== Upgrade Notice ==

= 1.1.0 =
The main plugin file has been renamed from `ru-consent-mode.php` to `consent-mode.php`.
The plugin namespace changed from `RUConsentMode\` to `ConsentMode\`.
The consent cookie name changed from `ru_consent_preferences` to `consent_preferences`.
If upgrading from 1.0.x: deactivate the old plugin entry and activate `consent-mode.php`.