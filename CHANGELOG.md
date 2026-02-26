# Changelog

Wszystkie istotne zmiany w projekcie **Universal Consent Mode (GCMv2)** dokumentowane są w tym pliku.

Format oparty na [Keep a Changelog](https://keepachangelog.com/pl/1.0.0/).  
Wersjonowanie zgodne z [Semantic Versioning](https://semver.org/lang/pl/).

---

## [Unreleased]

### Planowane
- Eksport/import ustawień (.json)
- Panel przeglądu logów diagnostycznych (WP_DEBUG)
- Integracja z popularnymi wtyczkami (WooCommerce, Contact Form 7)
- Unit testy PHPUnit

---

## [1.1.1] – 2026-02-26

### Naprawiono
- Błąd zapisu cookie: `.join('')` bez separatora powodował konkatenację `expires=` z wartością cookie, co uniemożliwiało odczyt po przeładowaniu strony → naprawiono na `.join('; ')`
- Usunięto parametr `domain=` z cookie – powodował konflikty na niektórych hostach Apache/nginx
- Samoleczenie: wykrycie i usunięcie uszkodzonego cookie przy parsowaniu (self-healing)
- Zabezpieczenie `init()`: jeśli baner i przycisk odwołania są oba ukryte → wymuszenie wyświetlenia banera
- Bump wersji `1.1.0` → `1.1.1` dla wymuszenia cache-bust (`?ver=1.1.1`)

---

## [1.1.0] – 2026-02-26

### Zmieniono (Breaking Changes)
- **Zmiana nazwy pliku głównego**: `ru-consent-mode.php` → `consent-mode.php`
- **Zmiana przestrzeni nazw PHP**: `RUConsentMode\` → `ConsentMode\`
- **Zmiana nazw stałych**: `RU_CONSENT_MODE_*` → `CONSENT_MODE_*`
- **Zmiana nazwy opcji WP**: `ru_consent_mode_settings` → `consent_mode_settings`
- **Zmiana nazwy cookie**: `ru_consent_preferences` → `consent_preferences`
- **Zmiana tekst domain**: `ru-consent-mode` → `consent-mode`
- **Zmiana obiektu JS**: `window.ruConsentMode` → `window.consentMode`
- Stary plik `ru-consent-mode.php` zastąpiony stubem deprecacji z komunikatem admina

> ⚠️ Użytkownicy wersji 1.0.x muszą dezaktywować stary plugin i aktywować
> `consent-mode.php`. Ustawienia w `wp_options` będą wymagały jednorazowego
> przepisania (zresetują się do domyślnych).

### Dodano
- Czwarty język: **Ukraiński (UA)** we wszystkich modułach i18n
- Natywny element `<dialog>` dla okna preferencji (bez bibliotek zewnętrznych)
- Model **3-przyciskowy**: Tylko niezbędne / Marketing / Akceptuj wszystko
- Przycisk "Dostosuj" otwierający modalne okno granularnych ustawień
- Stały przycisk odwołania zgody z atrybutem `hidden` (RODO art. 7)
- Podwójny atrybut blokowania skryptów: `data-rcm-consent` + `data-consent-category`
- Normalizacja nazw kategorii: `marketing→ads`, `advertising→ads`, `statistics→analytics`
- Language Manager – tabbed UI (EN/RU/UA/PL × 14 pól w panelu admina)
- Metoda `Consent::has_made_choice()` – wykrywa pierwszą wizytę
- Metoda `Consent::flush_cache()` – czyszczenie cache runtime
- Stałe `Consent::ALWAYS_GRANTED` = `['functionality_storage', 'security_storage']`
- Zdarzenie JS `consentUpdated` (CustomEvent z `detail = {ad_storage, ...}`)
- Publiczne API JS: `window.ConsentBanner.{openModal, showBanner, readConsentCookie, applyConsent}`
- `wp_script_add_data( $handle, 'consent-category', 'analytics' )` – rejestracja kategorii bez CSV
- Tryb debug: `Log::write()` i `Log::log_consent()` – tylko gdy `WP_DEBUG=true`
- Guard `is_admin()` w `Front::init()` – brak ładowania zasobów w panelu WP
- Autoloader with fallback dla instalacji bez Composer

### Naprawiono
- **ReferenceError: `saveBtn` is not defined** – deklaracja przeniesiona wewnątrz `attachEventListeners()`
- Rozbieżność nazw cookie między `Consent.php`, `Front.php` i `banner.js`
- Podwójne ładowanie zasobów banera w panelu administracyjnym
- Zduplikowana rejestracja sekcji ustawień w `Admin.php`
- Pozostałości starego kodu po nieudanym rewrite w pliku `banner.js` (obcięto do 560 linii)
- Dodatkowy `}` na końcu pliku `Consent.php` (unmatched brace error)
- Przycisk odwołania z `style="display:none"` → `hidden` atrybut (spójność z JS)
- `activate()`: usunięte TODO-komentarze dla operacji bazodanowych, dodano domyślne ustawienia

### Usunięto
- **Cały mechanizm AJAX**: `wp_ajax_ru_consent_mode_submit`, `sendConsentToBackend()`, `nonce`
- Tabele bazodanowe z `Log.php` (`$wpdb`, `create_table()`, `get_logs()`, `delete_old_logs()`)
- `DROP TABLE` i `global $wpdb` z `uninstall.php`
- Stary wzorzec obiektów w `banner.js` – zastąpiony przez moduł ES6+
- Metody AJAX z `Front.php`: `handle_consent_submission()`, `wp_ajax_*` hooks
- Stałe `STATUS.md` – przestarzałe notatki deweloperskie w języku rosyjskim

### Refaktoryzacja (bez zmiany API)
- `Consent.php`: kompletne przepisanie na czytnik bezstanowy – tylko `$_COOKIE`
- `Log.php`: usunięcie `$wpdb`, zastąpione przez `error_log()` gdy `WP_DEBUG`
- `uninstall.php`: wyłącznie `delete_option()`, brak operacji DB
- `banner.js`: kompletne przepisanie ES6+ (561 linii), bez jQuery, bez AJAX
- `Front.php`: nowy HTML banera, `<dialog>` modal, prywatne helpery i18n
- `ScriptGuard.php`: 3-etapowa rozdzielność kategorii, metoda `normalize_category()`
- `Admin.php`: Language Manager z tabbed UI, `sanitize_settings()` dla 4 języków

### Dokumentacja
- Nowy `README_PL.md` – kompleksowa dokumentacja polska z wzorcową polityką cookies
- Zaktualizowany `README.md` – usunięto referencje AJAX, dodano UA, zaktualizowano API
- Nowy plik `docs/POLITYKA_COOKIES.md` – samodzielna polityka cookies (RODO + PKE 2024)
- Zaktualizowany `readme.txt` – WordPress.org format
- Usunięty `STATUS.md` – przestarzałe notatki deweloperskie

---

## [1.0.0] – 2025-10-23

### Dodano – Moduł Bootstrap

#### Funkcjonalność podstawowa
- Moduł Bootstrap (`src/Consent/Bootstrap.php`)
  - Inicjalizacja Google Consent Mode v2
  - Tworzenie `window.dataLayer` z ochroną przed duplikacją
  - Stub funkcji `gtag()`
  - Domyślny stan zgody (podejście privacy-first): wszystkie `denied` poza `security_storage`
  - Integracja Google Tag Manager
  - Wsparcie parametrów GTM dla środowisk testowych

#### Integracja WordPress
- Hook `wp_head` (priorytet 0): inicjalizacja GCMv2, `ads_data_redaction`, `wait_for_update`
- Hook `wp_head` (priorytet 5): ładowanie kontenera GTM, walidacja ID formatu `GTM-*`
- Hook `wp_body_open`: fallback `<noscript>` GTM

#### Architektura projektu
- PSR-4 Autoloading przez Composer
- Przestrzeń nazw `ConsentMode\` (zaktualizowana w v1.1.0 z `RUConsentMode\`)
- WordPress Coding Standards (WPCS)
- GPL v2

---

## Legenda

| Symbol | Znaczenie |
|--------|-----------|
| ✅ | Zaimplementowane |
| ⚠️ | Zmiana łamiąca kompatybilność |
| 🔄 | W trakcie |
| 📋 | Zaplanowane |

---

[Unreleased]: https://github.com/yourname/consent-mode/compare/v1.1.1...HEAD
[1.1.1]: https://github.com/yourname/consent-mode/compare/v1.1.0...v1.1.1
[1.1.0]: https://github.com/yourname/consent-mode/compare/v1.0.0...v1.1.0
[1.0.0]: https://github.com/yourname/consent-mode/releases/tag/v1.0.0