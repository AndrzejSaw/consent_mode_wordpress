# Universal Consent Mode (GCMv2)

[![WordPress](https://img.shields.io/badge/WordPress-6.2%2B-blue)](https://wordpress.org/)
[![PHP](https://img.shields.io/badge/PHP-8.1%2B-purple)](https://www.php.net/)
[![License](https://img.shields.io/badge/License-GPL%20v2-green)](LICENSE.txt)
[![Google Consent Mode v2](https://img.shields.io/badge/Google%20Consent%20Mode-v2-orange)](https://developers.google.com/tag-platform/security/guides/consent)
[![GDPR](https://img.shields.io/badge/GDPR-compliant-brightgreen)](https://gdpr.eu/)

**Universal WordPress plugin for Google Consent Mode v2** â€” multilingual (EN, RU, UA, PL), stateless (No-DB), GDPR and Polish PKE 2024 compliant.

> đź‡µđź‡± **Dokumentacja w jÄ™zyku polskim:** [README_PL.md](README_PL.md)

---

## Features

- **3-button consent model**: Essential Only / Marketing / Accept All + Customize modal
- **Native `<dialog>`** for granular per-category preferences (no polyfill required)
- **No-DB Architecture**: consent stored in `consent_preferences` cookie only â€” no SQL tables
- **Google Consent Mode v2**: `gtag('consent', 'default'|'update', ...)` with all 7 parameters
- **Google Tag Manager**: optional auto-loading, validated GTM ID, noscript fallback
- **Script Guard**: blocks tracking scripts until consent, dual-attribute (`data-consent-category` + `data-rcm-consent`), auto-reactivation
- **4 Languages**: EN, RU, UA, PL â€” auto-detected from WordPress locale (WPML/Polylang compatible)
- **Admin Language Manager**: tabbed UI to edit all banner/modal texts per language
- **Revocation button**: permanent floating button on every page (GDPR Art. 7 compliant)
- **Geolocation**: Cloudflare headers / WP timezone fallback â€” strict mode for EU/EEA users
- **Accessibility**: ARIA roles, native focus trap, `:focus-visible` outlines

## Requirements

- WordPress 6.2+
- PHP 8.1+

## Quick Setup

1. Activate plugin (`consent-mode.php`)
2. Go to **Settings â†’ Consent Mode Settings**
3. Configure GTM Container ID (if needed)
4. Add script handles to Script Guard categories
5. Customize banner text in Language Manager tab

## PHP Usage

```php
// Check server-side consent (e.g., for inline analytics)
use ConsentMode\Consent\Consent;

if ( Consent::instance()->has_consent( 'analytics_storage' ) ) {
    // inject analytics
}
```

## JavaScript Events

```javascript
window.addEventListener( 'consentUpdated', ( e ) => {
    console.log( e.detail ); // { ad_storage: 'granted', ... }
} );

// Public API
window.ConsentBanner.openModal();
window.ConsentBanner.readConsentCookie();
```

## Plugin Structure

```
consent-mode/
â”śâ”€â”€ consent-mode.php              â† Main plugin file (v1.1.0+)
â”śâ”€â”€ src/
â”‚   â”śâ”€â”€ Admin/Admin.php           â† Settings API, Language Manager
â”‚   â”śâ”€â”€ Consent/Bootstrap.php     â† GCMv2 default state + GTM loader
â”‚   â”śâ”€â”€ Consent/Consent.php       â† Stateless cookie reader
â”‚   â”śâ”€â”€ Front/Front.php           â† Banner rendering, assets, i18n
â”‚   â”śâ”€â”€ Front/ScriptGuard.php     â† Script blocking/reactivation
â”‚   â””â”€â”€ Geo/Geo.php               â† Country detection
â”śâ”€â”€ assets/
â”‚   â”śâ”€â”€ css/banner.css
â”‚   â””â”€â”€ js/banner.js              â† ES6+, no jQuery, no AJAX
â””â”€â”€ docs/POLITYKA_COOKIES.md      â† Cookie policy template (Polish)
```

## Documentation

- đź‡µđź‡± **Polish**: [README_PL.md](README_PL.md) â€” full documentation including GDPR/PKE compliance and cookie policy template
- đź“‹ **Changelog**: [CHANGELOG.md](CHANGELOG.md)
- đźŤŞ **Cookie Policy (PL)**: [docs/POLITYKA_COOKIES.md](docs/POLITYKA_COOKIES.md)

## License

[GPL v2 or later](https://www.gnu.org/licenses/gpl-2.0.html)