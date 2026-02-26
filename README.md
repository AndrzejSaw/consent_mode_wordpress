# Universal Consent Mode (GCMv2)

[![WordPress](https://img.shields.io/badge/WordPress-6.2%2B-blue)](https://wordpress.org/)
[![PHP](https://img.shields.io/badge/PHP-8.1%2B-purple)](https://www.php.net/)
[![License](https://img.shields.io/badge/License-GPL%20v2-green)](LICENSE.txt)
[![Google Consent Mode v2](https://img.shields.io/badge/Google%20Consent%20Mode-v2-orange)](https://developers.google.com/tag-platform/security/guides/consent)

**Universal WordPress plugin for Google Consent Mode v2** with multilingual support (EN, RU, PL) and GDPR compliance for EU/EEA, Poland, Ukraine, Belarus, and Russia.

---

## 📋 Table of Contents

- [Features](#-features)
- [Requirements](#-requirements)
- [Installation](#-installation)
- [Quick Start](#-quick-start)
- [Configuration](#-configuration)
- [Usage](#-usage)
- [Plugin Structure](#-plugin-structure)
- [API & Hooks](#-api--hooks)
- [FAQ](#-faq)
- [Support](#-support)

---

## 🎯 Features

### Universal & Multilingual
- ✅ **Multilingual Support**: Built-in translations for English, Russian, and Polish.
- ✅ **Strict Mode**: Automatically applies strict GDPR-like consent rules for users in **EU/EEA, Poland, Ukraine, Belarus, and Russia**.
- ✅ **Customizable Content**: Edit banner titles, descriptions, and privacy policy links for each language via the Admin Panel.

### Google Consent Mode v2
- ✅ **Full Integration** with the latest Consent Mode API.
- ✅ **Default Consent State**: All categories denied by default in strict regions (except security_storage).
- ✅ **Update Consent**: Dynamic updates upon user choice.
- ✅ **7 Consent Categories**: analytics_storage, ad_storage, ad_user_data, ad_personalization, functionality_storage, personalization_storage, security_storage.

### Google Tag Manager
- ✅ **Automatic GTM Loading** respecting consent.
- ✅ **Load Priority**: Consent Mode (priority 0) → GTM (priority 5).
- ✅ **Noscript Fallback** for users without JavaScript.
- ✅ **Environment Support**: gtm_auth, gtm_preview, gtm_cookies_win.
- ✅ **DataLayer Protection**: Prevents duplication.

### Script Guard
- ✅ **Blocks Tracking Scripts** until consent is granted.
- ✅ **Categories**: analytics, ads, functional.
- ✅ **Auto Reactivation** after consent.
- ✅ **Attribute Preservation**: async, defer, crossorigin, integrity, nonce.
- ✅ **Supports External & Inline Scripts**.

### Consent Banner
- ✅ **Responsive Design** for all devices.
- ✅ **Three Buttons**: Accept All, Reject All, Customize.
- ✅ **Revocation Mechanism**: Floating button to re-open settings (GDPR compliant).
- ✅ **Detailed Configuration** via checkboxes.
- ✅ **Cookie Management** with configurable expiration.
- ✅ **AJAX Saving** of consent.

### Geolocation
- ✅ **CloudFlare Headers** (HTTP_CF_IPCOUNTRY).
- ✅ **Caching** of country detection (24 hours).
- ✅ **Extensible**: Placeholder for IP services.

### Admin Panel
- ✅ **Settings API** integration.
- ✅ **GTM Configuration** (container ID, enable/disable).
- ✅ **Category Mapping** (CSV lists of script handles).
- ✅ **Multilingual Content Settings**.
- ✅ **Validation & Sanitization**.

---

## 💻 Requirements

- **WordPress**: 6.2 or higher
- **PHP**: 8.1 or higher
- **Composer**: For development (optional)

---

## 📦 Installation

### Method 1: Via WordPress Admin

1. Download the plugin archive.
2. Go to **Plugins → Add New → Upload Plugin**.
3. Upload the ZIP file.
4. Click **Activate**.

### Method 2: Via FTP/SSH

```bash
# Upload the ru-consent-mode folder to wp-content/plugins/
cd /path/to/wordpress/wp-content/plugins/
unzip universal-consent-mode.zip

# If you need to install dev dependencies:
cd ru-consent-mode
composer install --no-dev
```

---

## 🚀 Quick Start

### Step 1: Activation
Upon activation, the plugin automatically:
- Initializes Google Consent Mode v2 (denied by default in strict regions).
- Adds a consent banner to the site.
- Blocks tracking scripts until consent is received.

### Step 2: GTM Configuration (Optional)

Go to **Settings → Universal Consent Mode**:

```
Google Tag Manager Settings
├── Enable GTM Loader: ✓
└── GTM Container ID: GTM-XXXXXXX
```

### Step 3: Script Category Configuration

In the **Script Guard Settings** section, specify script handles:

```
Analytics Scripts:
google-analytics, ga4, clarity, matomo

Advertising Scripts:
googletag, adsbygoogle, fb-pixel, twitter-pixel

Functional Scripts:
youtube-api, vimeo-player, google-maps
```

### Step 4: Multilingual Content

In the **Multilingual Content** section, you can customize the banner text for English, Russian, and Polish. If left empty, default translations are used.

### Step 5: Testing

1. Open the site in Incognito mode.
2. You will see the consent banner.
3. Open DevTools → Console.
4. Check for `dataLayer` and `gtag()`.
5. Click "Accept All" - scripts will activate.

---

## ⚙️ Configuration

### Main Parameters

The plugin saves settings in the `ru_consent_mode_settings` option:

```php
$settings = get_option('ru_consent_mode_settings', [
    'inject_gtm_loader' => false,
    'gtm_container_id' => '',
    'categories_map' => [
        'analytics' => 'ga4, clarity',
        'ads' => 'googletag, fb-pixel',
        'functional' => 'youtube, vimeo',
    ],
    'content' => [
        'en' => ['title' => '...', 'description' => '...'],
        'ru' => ['title' => '...', 'description' => '...'],
        'pl' => ['title' => '...', 'description' => '...'],
    ]
]);
```

### Programmatic Configuration

Add to `functions.php`:

```php
// Enable GTM
add_filter('ru_consent_mode_gtm_enabled', '__return_true');

// Set GTM Container ID
add_filter('ru_consent_mode_gtm_container_id', function() {
    return 'GTM-XXXXXXX';
});

// Add scripts to analytics category
add_filter('ru_consent_mode_categories_map', function($map) {
    $map['analytics'] = 'ga4, clarity, custom-analytics';
    return $map;
});
```

---

## 📖 Usage

### Registering Scripts with Categories

When enqueuing scripts, use handles from the settings:

```php
// Google Analytics 4
wp_enqueue_script(
    'ga4', // handle from categories_map['analytics']
    'https://www.googletagmanager.com/gtag/js?id=G-XXXXXXXXXX',
    [],
    null,
    true
);

// Facebook Pixel
wp_enqueue_script(
    'fb-pixel', // handle from categories_map['ads']
    'https://connect.facebook.net/en_US/fbevents.js',
    [],
    null,
    true
);
```

The plugin automatically:
1. Blocks the script (`type="text/plain"`).
2. Adds the attribute `data-rcm-consent="analytics"`.
3. Reactivates it after consent.

### Handling Consent in JavaScript

```javascript
// Check current consent
const consent = RUConsentBanner.getCookie('ru_consent_mode');
const data = JSON.parse(consent);

if (data.analytics_storage === 'granted') {
    // Initialize analytics
}

// Listen for consent updates
document.addEventListener('ruConsentUpdated', function(event) {
    console.log('New consent:', event.detail);
    
    if (event.detail.ad_storage === 'granted') {
        // Load ad scripts
    }
});
```

---

## 🏗️ Plugin Structure

```
ru-consent-mode/
├── ru-consent-mode.php          # Main plugin file
├── uninstall.php                # Cleanup on uninstall
├── composer.json                # Composer config
├── README.md                    # Documentation (English)
├── README_PL.md                 # Documentation (Polish)
├── CHANGELOG.md                 # Changelog
├── readme.txt                   # WordPress.org readme
│
├── src/                         # PHP Classes (PSR-4)
│   ├── Admin/
│   │   └── Admin.php           # Admin panel & settings
│   ├── Consent/
│   │   ├── Bootstrap.php       # Google Consent Mode v2 init
│   │   └── Consent.php         # Consent management
│   ├── Front/
│   │   ├── Front.php           # Frontend coordination
│   │   └── ScriptGuard.php     # Script blocking
│   ├── Geo/
│   │   └── Geo.php             # Geolocation
│   └── ...
│
├── assets/
│   ├── css/
│   │   └── banner.css          # Banner styles
│   └── js/
│       └── banner.js           # Banner JavaScript
│
└── languages/                   # Translations (i18n)
```

---

## ❓ FAQ

### How does script blocking work?
The plugin uses the `script_loader_tag` filter to modify HTML. Scripts are changed to `type="text/plain"` until consent is granted, then changed back to `text/javascript` and executed.

### Which scripts are blocked?
Only those whose handles are specified in the `categories_map` settings. Others load normally.

### Does it work without GTM?
Yes! Google Consent Mode v2 works independently of GTM. GTM is an optional integration.

### Is consent saved in the database?
In the current version, consent is stored only in a cookie. Database logging is planned.

---

## 🤝 Support

### Report a Bug
Create an issue on GitHub.

### Documentation
- [Google Consent Mode v2](https://developers.google.com/tag-platform/security/guides/consent)
- [GTM Integration](https://developers.google.com/tag-platform/tag-manager/web)

---

## 📄 License

This plugin is distributed under the **GPL v2 or later** license.

---

**Made with ❤️ for the WordPress Community**
