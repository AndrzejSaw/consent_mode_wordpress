# Universal Consent Mode (GCMv2)

[![WordPress](https://img.shields.io/badge/WordPress-6.2%2B-blue)](https://wordpress.org/)
[![PHP](https://img.shields.io/badge/PHP-8.1%2B-purple)](https://www.php.net/)
[![License](https://img.shields.io/badge/License-GPL%20v2-green)](LICENSE.txt)
[![Google Consent Mode v2](https://img.shields.io/badge/Google%20Consent%20Mode-v2-orange)](https://developers.google.com/tag-platform/security/guides/consent)
[![RODO/GDPR](https://img.shields.io/badge/RODO%2FGDPR-compliant-brightgreen)](https://gdpr.eu/)
[![PKE 2024](https://img.shields.io/badge/PKE-2024-blue)](https://isap.sejm.gov.pl/isap.nsf/DocDetails.xsp?id=WDU20240001221)

**Uniwersalna wtyczka WordPress** implementująca Google Consent Mode v2 z pełną obsługą wielojęzyczną (EN, RU, UA, PL), zgodna z RODO oraz polską Ustawą z dnia 12 lipca 2024 r. Prawo komunikacji elektronicznej (PKE, Dz. U. 2024 poz. 1221).

> **Architektura bezstanowa (No-DB):** preferencje użytkownika przechowywane wyłącznie w pliku cookie na urządzeniu końcowym – brak tabel bazodanowych i przetwarzania danych po stronie serwera.

---

## 📋 Spis treści

1. [Funkcje](#-funkcje)
2. [Wymagania techniczne](#-wymagania-techniczne)
3. [Instalacja](#-instalacja)
4. [Szybki start](#-szybki-start)
5. [Konfiguracja](#-konfiguracja)
6. [Użycie w motywie PHP](#-użycie-w-motywie-php)
7. [API – hooki i filtry](#-api--hooki-i-filtry)
8. [Struktura plików](#-struktura-plików)
9. [Zgodność z RODO i PKE](#-zgodność-z-rodo-i-pke)
10. [Polityka plików cookie (wzorzec)](#-polityka-plików-cookie-wzorzec)
11. [FAQ](#-faq)
12. [Changelog](#-changelog)
13. [Licencja](#-licencja)

---

## 🎯 Funkcje

### Platforma zarządzania zgodami (CMP)

| Funkcja | Status |
|---------|:------:|
| Baner – model **3-przyciskowy** (Tylko niezbędne / Marketing / Akceptuj wszystko) | ✅ |
| Natywne okno modalne `<dialog>` z granularnym wyborem kategorii zgody | ✅ |
| Stały przycisk odwołania zgody w stopce (wymóg art. 7 RODO) | ✅ |
| Obsługa **4 języków**: EN, RU, UA, PL – automatyczna detekcja locale WordPress | ✅ |
| Edycja wszystkich tekstów banera i okna modalnego z poziomu panelu admina | ✅ |
| Architektura bezstanowa: plik cookie `consent_preferences`, brak tabel SQL | ✅ |
| TTL pliku cookie: 365 dni, SameSite=Lax, Secure (HTTPS), HttpOnly: nie | ✅ |
| Natychmiastowa aktualizacja GCMv2 bez odświeżania strony | ✅ |
| Zdarzenie JS `consentUpdated` po każdej zmianie zgody | ✅ |
| Dostępność: ARIA role, focus trap w `<dialog>`, `focus-visible` | ✅ |

### Google Consent Mode v2

| Funkcja | Status |
|---------|:------:|
| `gtag('consent', 'default', ...)` w `wp_head` priorytet 0 | ✅ |
| `gtag('consent', 'update', ...)` po wyborze użytkownika – bez przeładowania | ✅ |
| 7 parametrów: `ad_storage` · `ad_user_data` · `ad_personalization` · `analytics_storage` · `functionality_storage` · `personalization_storage` · `security_storage` | ✅ |
| `ads_data_redaction: true` przy braku zgody na reklamy | ✅ |
| `url_passthrough` – raportowanie konwersji bez ciasteczek reklamowych | ✅ |
| Geolokalizacja (nagłówki Cloudflare / timezone WP) – tryb ścisły dla UE/EOG | ✅ |

### Google Tag Manager

| Funkcja | Status |
|---------|:------:|
| Automatyczne ładowanie kontenera GTM (konfigurowalny ID w panelu admina) | ✅ |
| Walidacja formatu ID (wyrażenie regularne `GTM-[A-Z0-9]+`) | ✅ |
| Kolejność ładowania: Consent Mode (prio. 0) → GTM (prio. 5) | ✅ |
| Fallback `<noscript>` w `wp_body_open` – dla przeglądarek bez JS | ✅ |
| Parametry środowisk testowych: `gtm_auth`, `gtm_preview`, `gtm_cookies_win` | ✅ |
| Zabezpieczenie przed duplikacją `dataLayer` i `gtag` | ✅ |

### Strażnik skryptów (Script Guard)

| Funkcja | Status |
|---------|:------:|
| Blokowanie skryptów śledzących do udzielenia zgody (`type="text/plain"`) | ✅ |
| Podwójny atrybut: `data-consent-category` i `data-rcm-consent` | ✅ |
| Kategorie: `analytics` · `ads` · `functional` | ✅ |
| Automatyczna reaktywacja zewnętrznych i inline skryptów po zgodzie | ✅ |
| Zachowanie atrybutów: `async`, `defer`, `crossorigin`, `integrity`, `nonce` | ✅ |
| Konfiguracja przez CSV uchwytów WP (`$handle`) w panelu admina | ✅ |
| Wsparcie dla `wp_script_add_data( $handle, 'consent-category', 'analytics' )` | ✅ |

---

## 💻 Wymagania techniczne

| Wymaganie | Minimalna wersja |
|-----------|-----------------|
| WordPress | 6.2 |
| PHP | 8.1 |
| Chrome / Edge | 37 / 79 (natywny `<dialog>`) |
| Firefox | 53 |
| Safari | 15.4 |
| Composer | Opcjonalnie – do środowiska deweloperskiego |

---

## 📦 Instalacja

### Metoda 1 – Ręczna (zalecana dla środowiska produkcyjnego)

```bash
# 1. Skopiuj katalog wtyczki do WordPress
cp -r consent-mode/ /var/www/html/wp-content/plugins/

# 2. W panelu WordPress: Wtyczki → Aktywuj "Universal Consent Mode"
# 3. Przejdź do: Ustawienia → Consent Mode Settings
```

> ⚠️ **Uwaga dotycząca nazwy pliku:** Plik główny wtyczki to `consent-mode.php`.
> Stary plik `ru-consent-mode.php` (wersja < 1.1.0) zawiera jedynie komunikat
> deprecacji. Jeśli miałeś aktywną starą wersję – dezaktywuj ją i aktywuj nową.

### Metoda 2 – Composer (środowisko deweloperskie)

```bash
cd wp-content/plugins/consent-mode
composer install --no-dev
```

### Weryfikacja instalacji

Po aktywacji sprawdź w źródle strony (przed `</head>`):

```html
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('consent', 'default', { ... });
</script>
```

---

## 🚀 Szybki start

### 1. Skonfiguruj GTM (opcjonalnie, jeśli używasz Google Tag Manager)

```
Ustawienia → Consent Mode Settings
└── Google Tag Manager Settings
    ├── ✅ Włącz ładowanie GTM
    └── GTM Container ID: GTM-XXXXXXX
```

### 2. Skonfiguruj Script Guard

```
Ustawienia → Consent Mode Settings
└── Script Guard Settings
    ├── Skrypty analityczne:  google-analytics, ga4
    ├── Skrypty reklamowe:    googletag, adsbygoogle
    └── Skrypty funkcjonalne: youtube-embed
```

Podaj **uchwyty WordPress** (`$handle` z `wp_enqueue_script()`), oddzielone przecinkami.

### 3. Przetestuj w przeglądarce (tryb incognito)

Otwórz DevTools → Console:

```javascript
// Wszystkie muszą zwracać poprawne wartości:
console.log(window.dataLayer);          // Array z domyślnym stanem zgody
console.log(typeof window.gtag);        // 'function'
document.getElementById('consent-banner'); // Widoczny baner

// Po kliknięciu "Akceptuj wszystko":
document.cookie; // Zawiera 'consent_preferences=...'
```

---

## ⚙️ Konfiguracja

### Sekcja 1: Google Tag Manager Settings

| Pole | Typ | Opis |
|------|-----|------|
| Włącz ładowanie GTM | `checkbox` | Czy wtyczka ma wstrzykiwać skrypt GTM? |
| GTM Container ID | `text` | ID w formacie `GTM-XXXXXXX`, walidowany regex |

### Sekcja 2: Script Guard Settings

| Pole | Typ | Opis |
|------|-----|------|
| Skrypty analityczne | `textarea` | CSV uchwytów WP blokowanych do zgody `analytics` |
| Skrypty reklamowe | `textarea` | CSV uchwytów WP blokowanych do zgody `ads` |
| Skrypty funkcjonalne | `textarea` | CSV uchwytów WP blokowanych do zgody `functional` |

### Sekcja 3: Zarządzanie treścią (Language Manager)

Zakładki: **EN · RU · UA · PL**

Każdy język obsługuje następujące pola:

| Identyfikator pola | Opis |
|-------------------|------|
| `title` | Nagłówek banera |
| `description` | Opis w banerze |
| `privacy_url` | URL polityki prywatności |
| `btn_essential` | Etykieta przycisku "Tylko niezbędne" |
| `btn_marketing` | Etykieta przycisku "Marketing" |
| `btn_accept_all` | Etykieta przycisku "Akceptuj wszystko" |
| `customize` | Etykieta linku "Dostosuj" |
| `save_preferences` | Etykieta przycisku "Zapisz" w oknie modal |
| `modal_title` | Tytuł okna modalnego |
| `cat_necessary_desc` | Opis kategorii "Niezbędne" |
| `cat_analytics` | Nazwa kategorii analitycznej |
| `cat_analytics_desc` | Opis kategorii analitycznej |
| `cat_marketing` | Nazwa kategorii marketingowej |
| `cat_marketing_desc` | Opis kategorii marketingowej |

---

## 🔧 Użycie w motywie PHP

### Sprawdzanie zgody (PHP)

```php
use ConsentMode\Consent\Consent;

// Sprawdzenie pojedynczego parametru GCMv2
if ( Consent::instance()->has_consent( 'analytics_storage' ) ) {
    echo '<script>/* kod Google Analytics */</script>';
}

if ( Consent::instance()->has_consent( 'ad_storage' ) ) {
    echo '<script>/* kod Google Ads / Meta Pixel */</script>';
}

// Pobranie pełnego obiektu zgody
$consent = Consent::instance()->get_consent();
// Zwraca: ['ad_storage' => 'granted'|'denied', 'analytics_storage' => ...]

// Sprawdzenie: czy użytkownik już dokonał wyboru?
if ( ! Consent::instance()->has_made_choice() ) {
    // Pierwsza wizyta – baner zostanie wyświetlony
}
```

### Rejestracja skryptu z kategorią zgody (PHP)

```php
add_action( 'wp_enqueue_scripts', function () {
    // Metoda 1: przez wp_script_add_data (zalecana)
    wp_enqueue_script( 'ga4', 'https://gtag.js?id=G-XXXXXXXX', [], null, true );
    wp_script_add_data( 'ga4', 'consent-category', 'analytics' );

    // Metoda 2: przez CSV w panelu admina – wpisz 'ga4' w polu Analytics Scripts
} );
```

### Blokowanie skryptów HTML (bez PHP)

```html
<!-- Skrypt zewnętrzny – zablokowany do zgody analitycznej  -->
<script type="text/plain" data-consent-category="analytics"
        src="https://www.googletagmanager.com/gtag/js?id=G-XXXXXXXX" async>
</script>

<!-- Skrypt inline – zablokowany do zgody marketingowej -->
<script type="text/plain" data-consent-category="ads">
    fbq('init', 'XXXXXXXXXXXXXXXX');
    fbq('track', 'PageView');
</script>
```

### JavaScript – zdarzenia i API

```javascript
// Nasłuchiwanie na zmianę zgody
window.addEventListener( 'consentUpdated', ( event ) => {
    const consent = event.detail;
    // consent.analytics_storage === 'granted' | 'denied'
    // consent.ad_storage === 'granted' | 'denied'
    console.log( 'Zgoda zaktualizowana:', consent );
} );

// API publiczne (window.ConsentBanner)
window.ConsentBanner.openModal();          // Otwarcie okna preferencji
window.ConsentBanner.showBanner();         // Ponowne wyświetlenie banera
window.ConsentBanner.readConsentCookie();  // Odczyt aktualnej zgody
window.ConsentBanner.applyConsent({ ... }); // Programowe ustawienie zgody
```

---

## 📌 API – hooki i filtry

### Filtry PHP

```php
/**
 * Modyfikacja domyślnego stanu zgody dla danego kraju.
 *
 * @param array  $defaults     Domyślny stan zgody (wszystkie 'denied' poza always_granted).
 * @param string $country_code Kod kraju ISO 3166-1 alpha-2 (np. 'PL', 'DE').
 */
add_filter( 'consent_mode_default_consent', function ( array $defaults, string $country_code ): array {
    // Przykład: dla Niemiec wymagaj ścisłej zgody na analytics
    if ( 'DE' === $country_code ) {
        $defaults['analytics_storage'] = 'denied';
    }
    return $defaults;
}, 10, 2 );

/**
 * Modyfikacja konfiguracji cookie banera.
 *
 * @param array $config Konfiguracja: name, expires, path, domain, samesite.
 */
add_filter( 'consent_mode_banner_config', function ( array $config ): array {
    $config['expires'] = 180; // Zmień TTL z 365 na 180 dni
    return $config;
} );
```

### Akcje WordPress

```php
// Wykonywana po załadowaniu wszystkich modułów wtyczki (plugins_loaded)
add_action( 'plugins_loaded', function () {
    if ( class_exists( 'ConsentMode\Consent\Consent' ) ) {
        // Moduły wtyczki są dostępne
    }
} );
```

---

## 📝 Struktura plików

```
consent-mode/
├── consent-mode.php              ← Plik główny wtyczki (v1.1.0+)
├── ru-consent-mode.php           ← Stub deprecacji – wyłącznie komunikat admina
├── uninstall.php                 ← Czyszczenie przy deinstalacji (delete_option only)
├── composer.json
│
├── src/
│   ├── Admin/Admin.php           ← Settings API, Language Manager (tabbed UI)
│   ├── Consent/
│   │   ├── Bootstrap.php         ← GCMv2 default state + GTM loader
│   │   └── Consent.php           ← Bezstanowy czytnik cookie (COOKIE_NAME='consent_preferences')
│   ├── Front/
│   │   ├── Front.php             ← Baner HTML, wp_localize_script, i18n
│   │   └── ScriptGuard.php       ← Blokowanie/reaktywacja skryptów, dual-attr
│   ├── Geo/Geo.php               ← Geolokalizacja (Cloudflare headers / WP timezone)
│   ├── Log/Log.php               ← Debug logger (WP_DEBUG only, stateless)
│   └── Support/Support.php       ← Klasa pomocnicza (stub)
│
├── assets/
│   ├── css/banner.css            ← Style banera + native dialog + responsive
│   └── js/banner.js              ← Baner ES6+, No-AJAX, native dialog API
│
├── docs/
│   └── POLITYKA_COOKIES.md       ← Samodzielny wzorzec polityki plików cookie
├── examples/settings-example.php
├── languages/
├── tests/manual/
├── README.md                     ← Dokumentacja angielska
├── README_PL.md                  ← Ten plik
└── CHANGELOG.md
```

---

## ⚖️ Zgodność z RODO i PKE

### Podstawy prawne

| Akt prawny | Artykuł | Zastosowanie |
|------------|---------|-------------|
| RODO (UE) 2016/679 | Art. 5 ust. 1 lit. c | Minimalizacja danych – architektura bezstanowa |
| RODO (UE) 2016/679 | Art. 6 ust. 1 lit. a | Zgoda jako podstawa przetwarzania dla kategorii opt-in |
| RODO (UE) 2016/679 | Art. 7 | Możliwość odwołania zgody w każdym momencie |
| RODO (UE) 2016/679 | Art. 25 | Privacy by Design i Privacy by Default |
| PKE 2024, Dz.U. 2024/1221 | Art. 399 | Pliki niezbędne – brak konieczności uzyskania zgody |
| PKE 2024, Dz.U. 2024/1221 | Art. 400 | Pliki opcjonalne – wymagana uprzednia zgoda (opt-in) |

### Zasady Privacy by Design (Art. 25 RODO)

| Zasada | Implementacja |
|--------|---------------|
| Minimalizacja danych | Brak baz danych; dane wyłącznie lokalnie na urządzeniu końcowym |
| Privacy by Default | Wszystkie kategorie opcjonalne domyślnie `denied` |
| Przejrzystość | 3-przyciskowy model bez ciemnych wzorców (dark patterns) |
| Integralność | `security_storage: granted` – zawsze aktywny |
| Zarządzanie | Przycisk odwołania dostępny na każdej podstronie |

### Model 3-przyciskowy

Zgodnie z zaleceniami EDPB (European Data Protection Board) i wytycznymi UODO możliwość odrzucenia cookies musi być równie łatwo dostępna co ich akceptacja:

```
┌──────────────────────────────────────────────────────────────┐
│ 🍪 Ta strona używa plików cookie                             │
│ [opis przeznaczenia]                 [Polityka prywatności →]│
├──────────────────────────────────────────────────────────────┤
│  [ Tylko niezbędne ]  [ Marketing ]  [ Akceptuj wszystko ▶ ] │
│                       [ Dostosuj ▽ ]                         │
└──────────────────────────────────────────────────────────────┘
```

- **Tylko niezbędne** – jednym kliknięciem odmawia wszystkich kategorii opcjonalnych
- **Marketing** / **Akceptuj wszystko** – przyznaje pełną zgodę
- **Dostosuj** → Okno modalne z checkboxami per kategoria

---

## 🍪 Polityka plików cookie (wzorzec)

> Poniższa polityka stanowi wzorzec do dostosowania. Treść należy zmodyfikować zgodnie ze specyfiką własnej strony internetowej, stosowanymi narzędziami analitycznymi i reklamowymi oraz danymi Administratora.

Pełna, samodzielna wersja dostępna w pliku: **[docs/POLITYKA_COOKIES.md](docs/POLITYKA_COOKIES.md)**

---

### POLITYKA UŻYWANIA PLIKÓW COOKIE I TECHNOLOGII ŚLEDZENIA

#### 1. Postanowienia ogólne i architektura systemu zarządzania zgodami

Niniejsza Strona internetowa stosuje pliki cookie i funkcjonalnie równoważne technologie lokalnego przechowywania danych w celu: (a) zapewnienia prawidłowego technicznego funkcjonowania zasobu; (b) zbierania zagregowanych wskaźników analitycznych; (c) realizacji strategii marketingu cyfrowego. Zarządzanie parametrami prywatności i preferencjami użytkownika końcowego odbywa się za pośrednictwem Platformy zarządzania zgodami (Consent Management Platform – CMP) zintegrowanej z protokołem Google Consent Mode v2.

System CMP zaprojektowany jest i działa na bazie **architektury bezstanowej** (stateless). Ustawienia preferencji użytkowników nie są przetwarzane, agregowane ani przechowywane w serwerowych relacyjnych bazach danych Administratora. Status udzielonej lub odmówionej zgody rejestrowany jest wyłącznie lokalnie, na końcowym urządzeniu telekomunikacyjnym użytkownika, w postaci technicznego pliku cookie. Rozwiązanie to zapewnia ścisłe przestrzeganie zasady minimalizacji danych i wbudowanej ochrony prywatności (Privacy by Design), zgodnie z art. 5 ust. 1 lit. c i art. 25 RODO.

#### 2. Podstawy prawno-normatywne przetwarzania danych

Przetwarzanie informacji z użyciem plików cookie opiera się na:

- **Rozporządzeniu Parlamentu Europejskiego i Rady (UE) 2016/679** z 27 kwietnia 2016 r. (RODO);
- **Ustawie z dnia 12 lipca 2024 r. – Prawo komunikacji elektronicznej** (PKE, Dz. U. 2024 poz. 1221).

Zgodnie z art. 399 PKE, stosowanie ściśle niezbędnych plików cookie **nie wymaga** uprzedniej zgody użytkownika. Aktywacja plików cookie kategorii analitycznych i marketingowych wymaga uprzedniej, dobrowolnej, konkretnej i świadomej zgody (art. 6 ust. 1 lit. a RODO w zw. z art. 400 PKE).

#### 3. Klasyfikacja stosowanych plików cookie

| Kategoria | Cel | Podstawa prawna | Czas przechowywania | Wymagana zgoda |
|-----------|-----|-----------------|---------------------|----------------|
| **Ściśle niezbędne** | Działanie strony, bezpieczeństwo, status CMP | Art. 399 PKE – interes prawnie uzasadniony | Sesja – 365 dni | Nie |
| **Analityczne** | Statystyki, Google Analytics, optymalizacja | Art. 6(1)(a) RODO + art. 400 PKE | Do 24 miesięcy | **Tak – opt-in** |
| **Marketingowe** | Google Ads, Meta Pixel, remarketing | Art. 6(1)(a) RODO + art. 400 PKE | Do 24 miesięcy | **Tak – opt-in** |

**Ściśle niezbędne:** Niezbędne do świadczenia usługi elektronicznej żądanej przez użytkownika. Obejmują pliki zapewniające bezpieczeństwo sesji, kierowanie ruchem i przechowywanie statusu zgody CMP. Nie podlegają dezaktywacji z poziomu interfejsu strony.

**Analityczne:** Zbierają zagregowane metryki statystyczne dotyczące korzystania ze strony (m.in. Google Analytics). Nie służą do bezpośredniej identyfikacji użytkowników. Aktywowane wyłącznie po udzieleniu wyraźnej zgody.

**Marketingowe:** Umożliwiają profilowanie i remarketing (m.in. Google Ads, Meta Pixel). Przekazują parametry zgody zewnętrznym dostawcom usług technologicznych. Aktywowane wyłącznie po udzieleniu wyraźnej zgody.

#### 4. Implementacja Google Consent Mode v2

Strona obsługuje protokół Google Consent Mode v2 do dynamicznej kontroli zachowania tagów Google w zależności od statusu zgody, przekazywanego przez parametry: `ad_user_data`, `ad_personalization`, `analytics_storage`, `ad_storage`.

Przy braku lub odwołaniu zgody na pliki analityczne i marketingowe, menedżer tagów inicjuje status odmowy (`denied`). W trybie `denied`: system blokuje dostęp do localStorage przeglądarki, uniemożliwiając odczyt/zapis plików cookie przez zewnętrzne usługi reklamowe; komunikacja z zewnętrznymi serwerami ograniczona jest wyłącznie do technicznych sygnałów ping, pozbawionych identyfikatorów marketingowych.

#### 5. Zarządzanie preferencjami i okres przechowywania danych

Użytkownik posiada pełne prawo do udzielenia, modyfikacji lub odwołania zgody w **dowolnym momencie**, bez negatywnych konsekwencji (art. 7 ust. 3 RODO). Odwołanie zgody ma skutek wyłącznie na przyszłość.

**Interfejs:** Stały interaktywny element (przycisk „Ustawienia prywatności" lub „Zarządzaj plikami cookie") w stopce każdej strony serwisu umożliwia dostęp do panelu CMP.

**Okres przechowywania:** Techniczny plik cookie `consent_preferences` przechowuje status zgody przez maksymalnie **12 miesięcy** od ostatniej modyfikacji. Po upływie tego okresu system inicjuje nowe zapytanie o zgodę.

**Administracja kliencka:** Użytkownik może samodzielnie audytować i usuwać pliki cookie przez ustawienia przeglądarki.

#### 6. Podprzetwarzanie i transgraniczne przekazywanie danych

Po udzieleniu zgody na kategorie analityczne lub marketingowe, metadane mogą być zbierane przez autoryzowanych dostawców technologicznych działających jako niezależni administratorzy lub podmioty przetwarzające (m.in. Google Ireland Ltd, Meta Platforms Ireland Ltd).

Pierwotne przetwarzanie odbywa się w EOG. Ewentualny transfer do krajów trzecich (m.in. USA) legitymizowany jest przez: certyfikację w ramach EU-U.S. Data Privacy Framework (DPF) i/lub Standardowe Klauzule Umowne (SCC) zatwierdzone przez Komisję Europejską.

---

## ❓ FAQ

**Czy wtyczka jest zgodna z polskim PKE 2024?**
Tak. Wtyczka implementuje model opt-in dla wszystkich kategorii opcjonalnych zgodnie z art. 400 PKE. Pliki niezbędne aktywne są bez zgody (art. 399 PKE).

**Czy tworzone są tabele w bazie danych?**
Nie. Preferencje przechowywane są w pliku cookie `consent_preferences` na urządzeniu końcowym. Ustawienia wtyczki zapisywane są w `wp_options` przez standardowe WordPress Settings API.

**Jakie języki są obsługiwane?**
Angielski (en), Rosyjski (ru), Ukraiński (ua), Polski (pl). Język dobierany automatycznie na podstawie locale WordPress. Kompatybilność z WPML i Polylang.

**Czy wtyczka działa z Google Tag Manager?**
Tak. GTM może być ładowany przez wtyczkę (opcja w panelu admina) lub manualnie. GCMv2 inicjalizowany jest zawsze przed GTM (priorytet 0 vs 5 w `wp_head`).

**Jak zmienić czas ważności pliku cookie?**
Użyj filtra `consent_mode_banner_config` i zmień pole `expires` (wartość w dniach).

**Czy istnieje mechanizm odwołania zgody po zamknięciu banera?**
Tak – po udzieleniu zgody w stopce strony pojawia się stały przycisk (ikona ciasteczka 🍪). Kliknięcie go ponownie wyświetla baner i pozwala zmienić lub cofnąć zgodę.

**Czy wtyczka jest dostępna dla użytkowników z niepełnosprawnościami?**
Baner i `<dialog>` zawierają role ARIA (`role="dialog"`, `aria-labelledby`, `aria-describedby`, `aria-expanded`), obsługę klawiatury (Escape zamyka modal, focus trap natywnego `<dialog>`), kontrastowe obramowania `:focus-visible`.

**Jak przetestować integrację z GCMv2?**
1. Otwórz stronę w trybie incognito
2. W konsoli DevTools sprawdź `window.dataLayer` – powinna być tablica z domyślnym stanem `denied`
3. Kliknij „Akceptuj wszystko" – sprawdź `window.dataLayer` dla `consent update`
4. Opcjonalnie: użyj Google Tag Assistant lub trybu podglądu GTM

---

## 📝 Changelog

Pełna historia zmian dostępna w pliku [CHANGELOG.md](CHANGELOG.md) – format zgodny z [Keep a Changelog](https://keepachangelog.com/pl/1.0.0/), wersjonowanie zgodne z [Semantic Versioning](https://semver.org/lang/pl/).

---

## 📄 Licencja

[GPL v2 lub nowsza](https://www.gnu.org/licenses/gpl-2.0.html) – zgodnie z wymogami ekosystemu WordPress.

```
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2,
as published by the Free Software Foundation.
```

---

*Dokumentacja aktualizowana: 2026-02-26 · Wersja wtyczki: 1.1.1 · Autor: Your Name*