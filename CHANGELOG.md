# Changelog

Wszystkie istotne zmiany w projekcie **Universal Consent Mode (GCMv2)** dokumentowane sÄ… w tym pliku.

Format oparty na [Keep a Changelog](https://keepachangelog.com/pl/1.0.0/).  
Wersjonowanie zgodne z [Semantic Versioning](https://semver.org/lang/pl/).

---

## [Unreleased]

### Planowane
- Eksport/import ustawieĹ„ (.json)
- Panel przeglÄ…du logĂłw diagnostycznych (WP_DEBUG)
- Integracja z popularnymi wtyczkami (WooCommerce, Contact Form 7)
- Unit testy PHPUnit

---

## [1.1.0] â€” 2026-02-26

### Zmieniono (Breaking Changes)
- **Zmiana nazwy pliku gĹ‚Ăłwnego**: `ru-consent-mode.php` â†’ `consent-mode.php`
- **Zmiana przestrzeni nazw PHP**: `RUConsentMode\` â†’ `ConsentMode\`
- **Zmiana nazw staĹ‚ych**: `RU_CONSENT_MODE_*` â†’ `CONSENT_MODE_*`
- **Zmiana nazwy opcji WP**: `ru_consent_mode_settings` â†’ `consent_mode_settings`
- **Zmiana nazwy cookie**: `ru_consent_preferences` â†’ `consent_preferences`
- **Zmiana tekst domain**: `ru-consent-mode` â†’ `consent-mode`
- **Zmiana obiektu JS**: `window.ruConsentMode` â†’ `window.consentMode`
- Stary plik `ru-consent-mode.php` zastÄ…piony stubem deprecacji z komunikatem admina

> âš ď¸Ź UĹĽytkownicy wersji 1.0.x muszÄ… dezaktywowaÄ‡ stary plugin i aktywowaÄ‡
> `consent-mode.php`. Ustawienia w `wp_options` bÄ™dÄ… wymagaĹ‚y jednorazowego
> przepisania (zresetujÄ… siÄ™ do domyĹ›lnych).

### Dodano
- Czwarty jÄ™zyk: **UkraiĹ„ski (UA)** we wszystkich moduĹ‚ach i18n
- Natywny element `<dialog>` dla okna preferencji (bez bibliotek zewnÄ™trznych)
- Model **3-przyciskowy**: Tylko niezbÄ™dne / Marketing / Akceptuj wszystko
- Przycisk "Dostosuj" otwierajÄ…cy modalne okno granularnych ustawieĹ„
- StaĹ‚y przycisk odwoĹ‚ania zgody z atrybutem `hidden` (RODO art. 7)
- PodwĂłjny atrybut blokowania skryptĂłw: `data-rcm-consent` + `data-consent-category`
- Normalizacja nazw kategorii: `marketingâ†’ads`, `advertisingâ†’ads`, `statisticsâ†’analytics`
- Language Manager â€” tabbed UI (EN/RU/UA/PL Ă— 14 pĂłl w panelu admina)
- Metoda `Consent::has_made_choice()` â€” wykrywa pierwszÄ… wizytÄ™
- Metoda `Consent::flush_cache()` â€” czyszczenie cache runtime
- StaĹ‚e `Consent::ALWAYS_GRANTED` = `['functionality_storage', 'security_storage']`
- Zdarzenie JS `consentUpdated` (CustomEvent z `detail = {ad_storage, ...}`)
- Publiczne API JS: `window.ConsentBanner.{openModal, showBanner, readConsentCookie, applyConsent}`
- `wp_script_add_data( $handle, 'consent-category', 'analytics' )` â€” rejestracja kategorii bez CSV
- Tryb debug: `Log::write()` i `Log::log_consent()` â€” tylko gdy `WP_DEBUG=true`
- Guard `is_admin()` w `Front::init()` â€” brak Ĺ‚adowania zasobĂłw w panelu WP
- Autoloader with fallback dla instalacji bez Composer

### Naprawiono
- **ReferenceError: `saveBtn` is not defined** â€” deklaracja przeniesiona wewnÄ…trz `attachEventListeners()`
- RozbieĹĽnoĹ›Ä‡ nazw cookie miÄ™dzy `Consent.php`, `Front.php` i `banner.js`
- PodwĂłjne Ĺ‚adowanie zasobĂłw banera w panelu administracyjnym
- Zduplikowana rejestracja sekcji ustawieĹ„ w `Admin.php`
- PozostaĹ‚oĹ›ci starego kodu po nieudanym rewrite w pliku `banner.js` (obciÄ™to do 560 linii)
- Dodatkowy `}` na koĹ„cu pliku `Consent.php` (unmatched brace error)
- Przycisk odwoĹ‚ania z `style="display:none"` â†’ `hidden` atrybut (spĂłjnoĹ›Ä‡ z JS)
- `activate()`: usuniÄ™te TODO-komentarze dla operacji bazodanowych, dodano domyĹ›lne ustawienia

### UsuniÄ™to
- **CaĹ‚y mechanizm AJAX**: `wp_ajax_ru_consent_mode_submit`, `sendConsentToBackend()`, `nonce`
- Tabele bazodanowe z `Log.php` (`$wpdb`, `create_table()`, `get_logs()`, `delete_old_logs()`)
- `DROP TABLE` i `global $wpdb` z `uninstall.php`
- Stary wzorzec obiektĂłw w `banner.js` â€” zastÄ…piony przez moduĹ‚ ES6+
- Metody AJAX z `Front.php`: `handle_consent_submission()`, `wp_ajax_*` hooks
- StaĹ‚e `STATUS.md` â€” przestarzaĹ‚e notatki deweloperskie w jÄ™zyku rosyjskim

### Refaktoryzacja (bez zmiany API)
- `Consent.php`: kompletny przepisanie na czytnik bezstanowy â€” tylko `$_COOKIE`
- `Log.php`: usuniecie `$wpdb`, zastÄ…pione przez `error_log()` gdy `WP_DEBUG`
- `uninstall.php`: wyĹ‚Ä…cznie `delete_option()`, brak operacji DB
- `banner.js`: kompletne przepisanie ES6+ (561 linii), bez jQuery, bez AJAX
- `Front.php`: nowy HTML banera, `<dialog>` modal, prywatne helpery i18n
- `ScriptGuard.php`: 3-etapowa rozdzielnoĹ›Ä‡ kategorii, metoda `normalize_category()`
- `Admin.php`: Language Manager z tabbed UI, `sanitize_settings()` dla 4 jÄ™zykĂłw

### Dokumentacja
- Nowy `README_PL.md` â€” kompleksowa dokumentacja polska z wzorcowÄ… politykÄ… cookies
- Zaktualizowany `README.md` â€” usuniÄ™to referencje AJAX, dodano UA, zaktualizowano API
- Nowy plik `docs/POLITYKA_COOKIES.md` â€” samodzielna polityka cookies (RODO + PKE 2024)
- Zaktualizowany `readme.txt` â€” WordPress.org format
- UsuniÄ™ty `STATUS.md` â€” przestarzaĹ‚e notatki deweloperskie

---

## [1.0.0] â€” 2025-10-23

### Dodano â€” ModuĹ‚ Bootstrap

#### FunkcjonalnoĹ›Ä‡ podstawowa
- ModuĹ‚ Bootstrap (`src/Consent/Bootstrap.php`)
  - Inicjalizacja Google Consent Mode v2
  - Tworzenie `window.dataLayer` z ochronÄ… przed duplikacjÄ…
  - Stub funkcji `gtag()`
  - DomyĹ›lny stan zgody (podejĹ›cie privacy-first): wszystkie `denied` poza `security_storage`
  - Integracja Google Tag Manager
  - Wsparcie parametrĂłw GTM dla Ĺ›rodowisk testowych

#### Integracja WordPress
- Hook `wp_head` (priorytet 0): inicjalizacja GCMv2, `ads_data_redaction`, `wait_for_update`
- Hook `wp_head` (priorytet 5): Ĺ‚adowanie kontenera GTM, walidacja ID formatu `GTM-*`
- Hook `wp_body_open`: fallback `<noscript>` GTM

#### Architektura projektu
- PSR-4 Autoloading przez Composer
- PrzestrzeĹ„ nazw `ConsentMode\` (zaktualizowana w v1.1.0 z `RUConsentMode\`)
- WordPress Coding Standards (WPCS)
- GPL v2

---

## Legenda

| Symbol | Znaczenie |
|--------|-----------|
| âś… | Zaimplementowane |
| âš ď¸Ź | Zmiana Ĺ‚amiÄ…ca kompatybilnoĹ›Ä‡ |
| đź”„ | W trakcie |
| đź“‹ | Zaplanowane |

---

[Unreleased]: https://github.com/yourname/consent-mode/compare/v1.1.0...HEAD
[1.1.0]: https://github.com/yourname/consent-mode/compare/v1.0.0...v1.1.0
[1.0.0]: https://github.com/yourname/consent-mode/releases/tag/v1.0.0