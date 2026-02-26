# Changelog

All notable changes to RU Consent Mode (GCMv2) will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Planned
- Admin panel UI for settings management
- Frontend consent banner with customization
- REST API endpoints for consent management
- Multi-language support (Russian, English, German, French)
- Consent logs viewer in admin panel
- Analytics dashboard for consent statistics
- Export/Import plugin settings
- Integration with popular WordPress plugins

## [1.0.0] - 2025-10-23

### Added - Bootstrap Module

#### Core Functionality
- ✅ **Bootstrap Module** (`src/Consent/Bootstrap.php`)
  - Google Consent Mode v2 initialization
  - dataLayer creation with duplicate prevention
  - gtag() function stub implementation
  - Default consent state (privacy-first approach)
  - Google Tag Manager integration
  - Environment-specific GTM parameters support

#### WordPress Integration
- ✅ **wp_head Hook (Priority 0)**
  - Creates `window.dataLayer` if not exists
  - Defines `gtag()` function
  - Sets default consent state:
    - `ad_storage`: denied
    - `ad_user_data`: denied
    - `ad_personalization`: denied
    - `analytics_storage`: denied
    - `functionality_storage`: granted
    - `personalization_storage`: denied
    - `security_storage`: granted
  - Applies `ads_data_redaction`: true
  - Configurable `wait_for_update` time

- ✅ **wp_head Hook (Priority 5)**
  - Loads GTM container when configured
  - Validates GTM container ID format (GTM-XXXXXXX)
  - Prevents duplicate GTM loading
  - Supports staging/preview parameters:
    - `gtm_auth`
    - `gtm_preview`
    - `gtm_cookies_win`

- ✅ **wp_body_open Hook**
  - GTM noscript iframe fallback
  - For users with JavaScript disabled

#### Configuration
- ✅ Settings structure via `wp_options`
  - `inject_gtm_loader` - Enable/disable GTM injection
  - `gtm_container_id` - GTM container ID
  - `gtm_auth` - Environment auth parameter
  - `gtm_preview` - Preview mode parameter
  - `gtm_cookies_win` - Cookies win parameter
  - `ads_data_redaction` - Enable ads data redaction
  - `url_passthrough` - Enable URL passthrough
  - `wait_for_update` - Wait time in milliseconds

#### Security
- ✅ GTM container ID validation (regex pattern)
- ✅ Output escaping (`esc_js()`, `esc_attr()`)
- ✅ JSON encoding with proper flags
- ✅ Duplicate loading prevention
- ✅ Error handling and logging

#### Documentation
- ✅ **Technical Documentation** (`TECHNICAL.md`)
  - Complete technical specification
  - Architecture overview
  - Module documentation
  - API reference
  - Security guidelines
  - Performance optimization
  - Deployment instructions

- ✅ **Quick Start Guide** (`docs/QUICK_START.md`)
  - Fast setup instructions
  - Configuration examples
  - Testing procedures
  - Troubleshooting guide

- ✅ **README Documentation** (`README.md`)
  - Project overview
  - Feature list
  - Installation guide
  - Quick start
  - Architecture diagram
  - Browser support

- ✅ **Examples** (`examples/`)
  - `settings-example.php` - Configuration examples
  - `README.md` - Usage examples and best practices

- ✅ **Testing Tools**
  - `tests/manual/test-page.html` - Interactive test page
  - Browser console testing guide
  - GTM Preview mode instructions

### Project Structure

#### Files Created
```
ru-consent-mode/
├── ru-consent-mode.php           ✅ Main plugin file
├── uninstall.php                 ✅ Uninstall script
├── composer.json                 ✅ Composer configuration
├── README.md                     ✅ Project readme
├── TECHNICAL.md                  ✅ Technical documentation
├── CHANGELOG.md                  ✅ This file
├── src/
│   ├── Admin/Admin.php          ✅ Admin module (skeleton)
│   ├── Front/Front.php          ✅ Front module (skeleton)
│   ├── Consent/
│   │   ├── Consent.php          ✅ Consent module (skeleton)
│   │   └── Bootstrap.php        ✅ Bootstrap module (implemented)
│   ├── Geo/Geo.php              ✅ Geo module (skeleton)
│   ├── Log/Log.php              ✅ Log module (skeleton)
│   └── Support/Support.php      ✅ Support module (skeleton)
├── assets/
│   ├── css/banner.css           ✅ Banner styles
│   ├── js/banner.js             ✅ Banner JavaScript
│   └── img/                     ✅ Images directory
├── languages/
│   └── README.md                ✅ Translation guide
├── docs/
│   ├── README.md                ✅ Documentation index
│   └── QUICK_START.md           ✅ Quick start guide
├── examples/
│   ├── README.md                ✅ Examples guide
│   └── settings-example.php     ✅ Configuration examples
├── tests/
│   └── manual/
│       └── test-page.html       ✅ Interactive test page
└── readme.txt                   ✅ WordPress.org readme
```

#### Namespace Structure
```
RUConsentMode\
├── Admin\           - Admin panel functionality
├── Front\           - Frontend user interface
├── Consent\         - Consent management
│   └── Bootstrap    - ✅ Consent Mode initialization
├── Geo\             - Geolocation detection
├── Log\             - Event logging
└── Support\         - Helper utilities
```

### Standards Compliance

- ✅ **WordPress 6.2+** compatibility
- ✅ **PHP 8.1+** requirement
- ✅ **WordPress Coding Standards** (WPCS)
- ✅ **PSR-4 Autoloading**
- ✅ **Semantic Versioning**
- ✅ **GPL v2** license

### Technical Highlights

#### Google Consent Mode v2 Integration
```javascript
// Default consent state (privacy-first)
gtag('consent', 'default', {
    'ad_storage': 'denied',
    'ad_user_data': 'denied',
    'ad_personalization': 'denied',
    'analytics_storage': 'denied',
    'functionality_storage': 'granted',
    'personalization_storage': 'denied',
    'security_storage': 'granted'
});

// Additional settings
gtag('set', 'ads_data_redaction', true);
```

#### GTM Container Loading
```javascript
// Async GTM loading with duplication check
if (!w.google_tag_manager || !w.google_tag_manager[i]) {
    // Load GTM container
}
```

#### No Duplication
```javascript
// Smart dataLayer detection
window.dataLayer = window.dataLayer || [];

// gtag() stub if not exists
if (typeof window.gtag === 'undefined') {
    window.gtag = gtag;
}
```

### Testing

- ✅ Manual testing via test page
- ✅ Browser console verification
- ✅ GTM Preview mode compatibility
- ✅ Multiple browser testing
- ⏳ Automated PHPUnit tests (planned)
- ⏳ Integration tests (planned)
- ⏳ E2E tests (planned)

### Known Limitations

- Admin panel UI not yet implemented
- Frontend banner not yet implemented
- Geolocation detection not yet implemented
- Consent logging not yet implemented
- REST API not yet implemented
- Multi-language support not yet implemented

### Migration Notes

This is the initial release. No migration required.

### Breaking Changes

None - initial release.

---

## Version History Summary

| Version | Date | Status | Key Features |
|---------|------|--------|--------------|
| 1.0.0 | 2025-10-23 | ✅ Released | Bootstrap module, GTM integration, Consent Mode v2 |
| 1.1.0 | TBD | 🔄 Planned | Admin panel, Frontend banner, REST API |
| 1.2.0 | TBD | 📋 Planned | Multi-language, Analytics, Export/Import |
| 2.0.0 | TBD | 📋 Planned | Multi-site, Advanced features |

---

## Legend

- ✅ Implemented
- 🔄 In Progress
- 📋 Planned
- ⏳ Pending
- ❌ Deprecated/Removed

---

## Links

- [Unreleased]: https://github.com/yourname/ru-consent-mode/compare/v1.0.0...HEAD
- [1.0.0]: https://github.com/yourname/ru-consent-mode/releases/tag/v1.0.0

---

**Maintained by:** RU Consent Mode Team  
**License:** GPL v2 or later  
**Documentation:** [README.md](README.md) | [TECHNICAL.md](TECHNICAL.md)
