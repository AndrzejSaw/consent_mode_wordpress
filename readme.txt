=== Universal Consent Mode (GCMv2) ===
Contributors: yourname
Tags: gdpr, consent, google consent mode, privacy, gcm v2, cookie banner, multilingual
Requires at least: 6.2
Tested up to: 6.7
Requires PHP: 8.1
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Universal WordPress plugin for Google Consent Mode v2 with multilingual support (EN, RU, PL) and strict GDPR compliance for EU/EEA.

== Description ==

Universal Consent Mode (GCMv2) is a comprehensive WordPress plugin designed to help website owners comply with privacy legislation (GDPR, Russian Law) while implementing Google Consent Mode v2.

It is specifically tailored for websites operating in **Poland, Ukraine, Belarus, Russia, and the EU/EEA**, offering strict consent enforcement and multilingual support out of the box.

= Features =

* **Universal & Multilingual**: Built-in support for English, Russian, and Polish languages.
* **Strict Mode**: Automatically applies strict GDPR-like consent rules (denied by default) for users in EU/EEA, Poland, Ukraine, Belarus, and Russia.
* **Google Consent Mode v2 Integration**: Fully compatible with Google's latest consent framework.
* **Google Tag Manager**: Automatic GTM loading with consent respect.
* **Script Guard**: Blocks tracking scripts (Analytics, Ads, etc.) until consent is granted.
* **Geolocation Detection**: Automatically detects user location via CloudFlare headers.
* **Customizable Consent Banner**: Flexible banner display with customizable text via Admin Panel.
* **Revocation Mechanism**: Floating button to allow users to change their preferences at any time.

= Consent Types Supported =

* ad_storage
* ad_user_data
* ad_personalization
* analytics_storage
* functionality_storage
* personalization_storage
* security_storage

== Installation ==

1. Upload the `ru-consent-mode` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Navigate to **Settings > Universal Consent Mode** to configure the plugin.
4. Configure GTM ID and Script Guard categories.
5. Customize banner text in the **Multilingual Content** section if needed.

== Frequently Asked Questions ==

= What is Google Consent Mode v2? =

Google Consent Mode v2 is Google's framework for managing user consent for cookies and data collection, ensuring compliance with privacy regulations.

= Which languages are supported? =

The plugin comes with built-in support for **English (en)**, **Russian (ru)**, and **Polish (pl)**. The banner automatically displays the correct language based on your site's locale. You can also customize the text for each language in the settings.

= Does this plugin work with Google Tag Manager? =

Yes, the plugin is fully compatible with Google Tag Manager and will update consent state via the dataLayer. It can also load the GTM container for you.

= How does Script Guard work? =

You can list script handles (e.g., `google-analytics`, `fb-pixel`) in the settings. The plugin will block these scripts from loading until the user grants the appropriate consent.

== Screenshots ==

1. Consent banner (English)
2. Consent banner (Russian)
3. Admin settings page - Multilingual Content

== Changelog ==

= 1.0.0 =
* Initial release as Universal Consent Mode.
* Google Consent Mode v2 support.
* Multilingual support (EN, RU, PL).
* Strict mode for EU/EEA, PL, UA, BY, RU.
* Script Guard module.
* Geolocation detection.

== Upgrade Notice ==

= 1.0.0 =
Initial release of Universal Consent Mode plugin.
