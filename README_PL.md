# Universal Consent Mode (GCMv2)

[![WordPress](https://img.shields.io/badge/WordPress-6.2%2B-blue)](https://wordpress.org/)
[![PHP](https://img.shields.io/badge/PHP-8.1%2B-purple)](https://www.php.net/)
[![License](https://img.shields.io/badge/License-GPL%20v2-green)](LICENSE.txt)
[![Google Consent Mode v2](https://img.shields.io/badge/Google%20Consent%20Mode-v2-orange)](https://developers.google.com/tag-platform/security/guides/consent)
[![RODO/GDPR](https://img.shields.io/badge/RODO%2FGDPR-compliant-brightgreen)](https://gdpr.eu/)
[![PKE 2024](https://img.shields.io/badge/PKE-2024-blue)](https://isap.sejm.gov.pl/isap.nsf/DocDetails.xsp?id=WDU20240001221)

**Uniwersalna wtyczka WordPress** implementujÄ…ca Google Consent Mode v2 z peĹ‚nÄ… obsĹ‚ugÄ… wielojÄ™zycznÄ… (EN, RU, UA, PL), zgodna z RODO oraz polskÄ… UstawÄ… z dnia 12 lipca 2024 r. Prawo komunikacji elektronicznej (PKE, Dz. U. 2024 poz. 1221).

> **Architektura bezstanowa (No-DB):** preferencje uĹĽytkownika przechowywane wyĹ‚Ä…cznie w pliku cookie na urzÄ…dzeniu koĹ„cowym â€” brak tabel bazodanowych i przetwarzania danych po stronie serwera.

---

## đź“‹ Spis treĹ›ci

1. [Funkcje](#-funkcje)
2. [Wymagania techniczne](#-wymagania-techniczne)
3. [Instalacja](#-instalacja)
4. [Szybki start](#-szybki-start)
5. [Konfiguracja](#-konfiguracja)
6. [UĹĽycie w motywie PHP](#-uĹĽycie-w-motywie-php)
7. [API â€” hooki i filtry](#-api--hooki-i-filtry)
8. [Struktura plikĂłw](#-struktura-plikĂłw)
9. [ZgodnoĹ›Ä‡ z RODO i PKE](#-zgodnoĹ›Ä‡-z-rodo-i-pke)
10. [Polityka plikĂłw cookie (wzorzec)](#-polityka-plikĂłw-cookie-wzorzec)
11. [FAQ](#-faq)
12. [Changelog](#-changelog)
13. [Licencja](#-licencja)

---

## đźŽŻ Funkcje

### Platforma zarzÄ…dzania zgodami (CMP)

| Funkcja | Status |
|---------|:------:|
| Baner â€” model **3-przyciskowy** (Tylko niezbÄ™dne / Marketing / Akceptuj wszystko) | âś… |
| Natywne okno modalne `<dialog>` z granularnym wyborem kategorii zgody | âś… |
| StaĹ‚y przycisk odwoĹ‚ania zgody w stopce (wymĂłg art. 7 RODO) | âś… |
| ObsĹ‚uga **4 jÄ™zykĂłw**: EN, RU, UA, PL â€” automatyczna detekcja locale WordPress | âś… |
| Edycja wszystkich tekstĂłw banera i okna modalnego z poziomu panelu admina | âś… |
| Architektura bezstanowa: plik cookie `consent_preferences`, brak tabel SQL | âś… |
| TTL pliku cookie: 365 dni, SameSite=Lax, Secure (HTTPS), HttpOnly: nie | âś… |
| Natychmiastowa aktualizacja GCMv2 bez odĹ›wieĹĽania strony | âś… |
| Zdarzenie JS `consentUpdated` po kaĹĽdej zmianie zgody | âś… |
| DostÄ™pnoĹ›Ä‡: ARIA role, focus trap w `<dialog>`, `focus-visible` | âś… |

### Google Consent Mode v2

| Funkcja | Status |
|---------|:------:|
| `gtag('consent', 'default', ...)` w `wp_head` priorytet 0 | âś… |
| `gtag('consent', 'update', ...)` po wyborze uĹĽytkownika â€” bez przeĹ‚adowania | âś… |
| 7 parametrĂłw: `ad_storage` Â· `ad_user_data` Â· `ad_personalization` Â· `analytics_storage` Â· `functionality_storage` Â· `personalization_storage` Â· `security_storage` | âś… |
| `ads_data_redaction: true` przy braku zgody na reklamy | âś… |
| `url_passthrough` â€” raportowanie konwersji bez ciasteczek reklamowych | âś… |
| Geolokalizacja (nagĹ‚Ăłwki Cloudflare / timezone WP) â€” tryb Ĺ›cisĹ‚y dla UE/EOG | âś… |

### Google Tag Manager

| Funkcja | Status |
|---------|:------:|
| Automatyczne Ĺ‚adowanie kontenera GTM (konfigurowalny ID w panelu admina) | âś… |
| Walidacja formatu ID (wyraĹĽenie regularne `GTM-[A-Z0-9]+`) | âś… |
| KolejnoĹ›Ä‡ Ĺ‚adowania: Consent Mode (prio. 0) â†’ GTM (prio. 5) | âś… |
| Fallback `<noscript>` w `wp_body_open` â€” dla przeglÄ…darek bez JS | âś… |
| Parametry Ĺ›rodowisk testowych: `gtm_auth`, `gtm_preview`, `gtm_cookies_win` | âś… |
| Zabezpieczenie przed duplikacjÄ… `dataLayer` i `gtag` | âś… |

### StraĹĽnik skryptĂłw (Script Guard)

| Funkcja | Status |
|---------|:------:|
| Blokowanie skryptĂłw Ĺ›ledzÄ…cych do udzielenia zgody (`type="text/plain"`) | âś… |
| PodwĂłjny atrybut: `data-consent-category` i `data-rcm-consent` | âś… |
| Kategorie: `analytics` Â· `ads` Â· `functional` | âś… |
| Automatyczna reaktywacja zewnÄ™trznych i inline skryptĂłw po zgodzie | âś… |
| Zachowanie atrybutĂłw: `async`, `defer`, `crossorigin`, `integrity`, `nonce` | âś… |
| Konfiguracja przez CSV uchwytĂłw WP (`$handle`) w panelu admina | âś… |
| Wsparcie dla `wp_script_add_data( $handle, 'consent-category', 'analytics' )` | âś… |

---

## đź’» Wymagania techniczne

| Wymaganie | Minimalna wersja |
|-----------|-----------------|
| WordPress | 6.2 |
| PHP | 8.1 |
| Chrome / Edge | 37 / 79 (natywny `<dialog>`) |
| Firefox | 53 |
| Safari | 15.4 |
| Composer | Opcjonalnie â€” do Ĺ›rodowiska deweloperskiego |

---

## đź“¦ Instalacja

### Metoda 1 â€” RÄ™czna (zalecana dla Ĺ›rodowiska produkcyjnego)

```bash
# 1. Skopiuj katalog wtyczki do WordPress
cp -r consent-mode/ /var/www/html/wp-content/plugins/

# 2. W panelu WordPress: Wtyczki â†’ Aktywuj "Universal Consent Mode"
# 3. PrzejdĹş do: Ustawienia â†’ Consent Mode Settings
```

> âš ď¸Ź **Uwaga dotyczÄ…ca nazwy pliku:** Plik gĹ‚Ăłwny wtyczki to `consent-mode.php`.
> Stary plik `ru-consent-mode.php` (wersja < 1.1.0) zawiera jedynie komunikat
> deprecacji. JeĹ›li miaĹ‚eĹ› aktywnÄ… starÄ… wersjÄ™ â€” dezaktywuj jÄ… i aktywuj nowÄ….

### Metoda 2 â€” Composer (Ĺ›rodowisko deweloperskie)

```bash
cd wp-content/plugins/consent-mode
composer install --no-dev
```

### Weryfikacja instalacji

Po aktywacji sprawdĹş w ĹşrĂłdle strony (przed `</head>`):

```html
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('consent', 'default', { ... });
</script>
```

---

## đźš€ Szybki start

### 1. Skonfiguruj GTM (opcjonalnie, jeĹ›li uĹĽywasz Google Tag Manager)

```
Ustawienia â†’ Consent Mode Settings
â””â”€â”€ Google Tag Manager Settings
    â”śâ”€â”€ âś… WĹ‚Ä…cz Ĺ‚adowanie GTM
    â””â”€â”€ GTM Container ID: GTM-XXXXXXX
```

### 2. Skonfiguruj Script Guard

```
Ustawienia â†’ Consent Mode Settings
â””â”€â”€ Script Guard Settings
    â”śâ”€â”€ Skrypty analityczne:  google-analytics, ga4
    â”śâ”€â”€ Skrypty reklamowe:    googletag, adsbygoogle
    â””â”€â”€ Skrypty funkcjonalne: youtube-embed
```

Podaj **uchwyty WordPress** (`$handle` z `wp_enqueue_script()`), oddzielone przecinkami.

### 3. Przetestuj w przeglÄ…darce (tryb incognito)

OtwĂłrz DevTools â†’ Console:

```javascript
// Wszystkie muszÄ… zwracaÄ‡ poprawne wartoĹ›ci:
console.log(window.dataLayer);          // Array z domyĹ›lnym stanem zgody
console.log(typeof window.gtag);        // 'function'
document.getElementById('consent-banner'); // Widoczny baner

// Po klikniÄ™ciu "Akceptuj wszystko":
document.cookie; // Zawiera 'consent_preferences=...'
```

---

## âš™ď¸Ź Konfiguracja

### Sekcja 1: Google Tag Manager Settings

| Pole | Typ | Opis |
|------|-----|------|
| WĹ‚Ä…cz Ĺ‚adowanie GTM | `checkbox` | Czy wtyczka ma wstrzykiwaÄ‡ skrypt GTM? |
| GTM Container ID | `text` | ID w formacie `GTM-XXXXXXX`, walidowany regex |

### Sekcja 2: Script Guard Settings

| Pole | Typ | Opis |
|------|-----|------|
| Skrypty analityczne | `textarea` | CSV uchwytĂłw WP blokowanych do zgody `analytics` |
| Skrypty reklamowe | `textarea` | CSV uchwytĂłw WP blokowanych do zgody `ads` |
| Skrypty funkcjonalne | `textarea` | CSV uchwytĂłw WP blokowanych do zgody `functional` |

### Sekcja 3: ZarzÄ…dzanie treĹ›ciÄ… (Language Manager)

ZakĹ‚adki: **EN Â· RU Â· UA Â· PL**

KaĹĽdy jÄ™zyk obsĹ‚uguje nastÄ™pujÄ…ce pola:

| Identyfikator pola | Opis |
|-------------------|------|
| `title` | NagĹ‚Ăłwek banera |
| `description` | Opis w banerze |
| `privacy_url` | URL polityki prywatnoĹ›ci |
| `btn_essential` | Etykieta przycisku "Tylko niezbÄ™dne" |
| `btn_marketing` | Etykieta przycisku "Marketing" |
| `btn_accept_all` | Etykieta przycisku "Akceptuj wszystko" |
| `customize` | Etykieta linku "Dostosuj" |
| `save_preferences` | Etykieta przycisku "Zapisz" w oknie modal |
| `modal_title` | TytuĹ‚ okna modalnego |
| `cat_necessary_desc` | Opis kategorii "NiezbÄ™dne" |
| `cat_analytics` | Nazwa kategorii analitycznej |
| `cat_analytics_desc` | Opis kategorii analitycznej |
| `cat_marketing` | Nazwa kategorii marketingowej |
| `cat_marketing_desc` | Opis kategorii marketingowej |

---

## đź”§ UĹĽycie w motywie PHP

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

// Pobranie peĹ‚nego obiektu zgody
$consent = Consent::instance()->get_consent();
// Zwraca: ['ad_storage' => 'granted'|'denied', 'analytics_storage' => ...]

// Sprawdzenie: czy uĹĽytkownik juĹĽ dokonaĹ‚ wyboru?
if ( ! Consent::instance()->has_made_choice() ) {
    // Pierwsza wizyta â€” baner zostanie wyĹ›wietlony
}
```

### Rejestracja skryptu z kategoriÄ… zgody (PHP)

```php
add_action( 'wp_enqueue_scripts', function () {
    // Metoda 1: przez wp_script_add_data (zalecana)
    wp_enqueue_script( 'ga4', 'https://gtag.js?id=G-XXXXXXXX', [], null, true );
    wp_script_add_data( 'ga4', 'consent-category', 'analytics' );

    // Metoda 2: przez CSV w panelu admina â€” wpisz 'ga4' w polu Analytics Scripts
} );
```

### Blokowanie skryptĂłw HTML (bez PHP)

```html
<!-- Skrypt zewnÄ™trzny â€” zablokowany do zgody analitycznej  -->
<script type="text/plain" data-consent-category="analytics"
        src="https://www.googletagmanager.com/gtag/js?id=G-XXXXXXXX" async>
</script>

<!-- Skrypt inline â€” zablokowany do zgody marketingowej -->
<script type="text/plain" data-consent-category="ads">
    fbq('init', 'XXXXXXXXXXXXXXXX');
    fbq('track', 'PageView');
</script>
```

### JavaScript â€” zdarzenia i API

```javascript
// NasĹ‚uchiwanie na zmianÄ™ zgody
window.addEventListener( 'consentUpdated', ( event ) => {
    const consent = event.detail;
    // consent.analytics_storage === 'granted' | 'denied'
    // consent.ad_storage === 'granted' | 'denied'
    console.log( 'Zgoda zaktualizowana:', consent );
} );

// API publiczne (window.ConsentBanner)
window.ConsentBanner.openModal();          // Otwarcie okna preferencji
window.ConsentBanner.showBanner();         // Ponowne wyĹ›wietlenie banera
window.ConsentBanner.readConsentCookie();  // Odczyt aktualnej zgody
window.ConsentBanner.applyConsent({ ... }); // Programowe ustawienie zgody
```

---

## đź”Ś API â€” hooki i filtry

### Filtry PHP

```php
/**
 * Modyfikacja domyĹ›lnego stanu zgody dla danego kraju.
 *
 * @param array  $defaults     DomyĹ›lny stan zgody (wszystkie 'denied' poza always_granted).
 * @param string $country_code Kod kraju ISO 3166-1 alpha-2 (np. 'PL', 'DE').
 */
add_filter( 'consent_mode_default_consent', function ( array $defaults, string $country_code ): array {
    // PrzykĹ‚ad: dla Niemiec wymagaj Ĺ›cisĹ‚ej zgody na analytics
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
    $config['expires'] = 180; // ZmieĹ„ TTL z 365 na 180 dni
    return $config;
} );
```

### Akcje WordPress

```php
// Wykonywana po zaĹ‚adowaniu wszystkich moduĹ‚Ăłw wtyczki (plugins_loaded)
add_action( 'plugins_loaded', function () {
    if ( class_exists( 'ConsentMode\Consent\Consent' ) ) {
        // ModuĹ‚y wtyczki sÄ… dostÄ™pne
    }
} );
```

---

## đź“ Struktura plikĂłw

```
consent-mode/
â”śâ”€â”€ consent-mode.php              â† Plik gĹ‚Ăłwny wtyczki (v1.1.0+)
â”śâ”€â”€ ru-consent-mode.php           â† Stub deprecacji â€” wyĹ‚Ä…cznie komunikat admina
â”śâ”€â”€ uninstall.php                 â† Czyszczenie przy deinstalacji (delete_option only)
â”śâ”€â”€ composer.json
â”‚
â”śâ”€â”€ src/
â”‚   â”śâ”€â”€ Admin/Admin.php           â† Settings API, Language Manager (tabbed UI)
â”‚   â”śâ”€â”€ Consent/
â”‚   â”‚   â”śâ”€â”€ Bootstrap.php         â† GCMv2 default state + GTM loader
â”‚   â”‚   â””â”€â”€ Consent.php           â† Bezstanowy czytnik cookie (COOKIE_NAME='consent_preferences')
â”‚   â”śâ”€â”€ Front/
â”‚   â”‚   â”śâ”€â”€ Front.php             â† Baner HTML, wp_localize_script, i18n
â”‚   â”‚   â””â”€â”€ ScriptGuard.php       â† Blokowanie/reaktywacja skryptĂłw, dual-attr
â”‚   â”śâ”€â”€ Geo/Geo.php               â† Geolokalizacja (Cloudflare headers / WP timezone)
â”‚   â”śâ”€â”€ Log/Log.php               â† Debug logger (WP_DEBUG only, stateless)
â”‚   â””â”€â”€ Support/Support.php       â† Klasa pomocnicza (stub)
â”‚
â”śâ”€â”€ assets/
â”‚   â”śâ”€â”€ css/banner.css            â† Style banera + native dialog + responsive
â”‚   â””â”€â”€ js/banner.js              â† Baner ES6+, No-AJAX, native dialog API
â”‚
â”śâ”€â”€ docs/
â”‚   â””â”€â”€ POLITYKA_COOKIES.md       â† Samodzielny wzorzec polityki plikĂłw cookie
â”śâ”€â”€ examples/settings-example.php
â”śâ”€â”€ languages/
â”śâ”€â”€ tests/manual/
â”śâ”€â”€ README.md                     â† Dokumentacja angielska
â”śâ”€â”€ README_PL.md                  â† Ten plik
â””â”€â”€ CHANGELOG.md
```

---

## âš–ď¸Ź ZgodnoĹ›Ä‡ z RODO i PKE

### Podstawy prawne

| Akt prawny | ArtykuĹ‚ | Zastosowanie |
|------------|---------|-------------|
| RODO (UE) 2016/679 | Art. 5 ust. 1 lit. c | Minimalizacja danych â€” architektura bezstanowa |
| RODO (UE) 2016/679 | Art. 6 ust. 1 lit. a | Zgoda jako podstawa przetwarzania dla kategorii opt-in |
| RODO (UE) 2016/679 | Art. 7 | MoĹĽliwoĹ›Ä‡ odwoĹ‚ania zgody w kaĹĽdym momencie |
| RODO (UE) 2016/679 | Art. 25 | Privacy by Design i Privacy by Default |
| PKE 2024, Dz.U. 2024/1221 | Art. 399 | Pliki niezbÄ™dne â€” brak koniecznoĹ›ci uzyskania zgody |
| PKE 2024, Dz.U. 2024/1221 | Art. 400 | Pliki opcjonalne â€” wymagana uprzednia zgoda (opt-in) |

### Zasady Privacy by Design (Art. 25 RODO)

| Zasada | Implementacja |
|--------|---------------|
| Minimalizacja danych | Brak baz danych; dane wyĹ‚Ä…cznie lokalnie na urzÄ…dzeniu koĹ„cowym |
| Privacy by Default | Wszystkie kategorie opcjonalne domyĹ›lnie `denied` |
| PrzejrzystoĹ›Ä‡ | 3-przyciskowy model bez ciemnych wzorcĂłw (dark patterns) |
| IntegralnoĹ›Ä‡ | `security_storage: granted` â€” zawsze aktywny |
| ZarzÄ…dzanie | Przycisk odwoĹ‚ania dostÄ™pny na kaĹĽdej podstronie |

### Model 3-przyciskowy

Zgodnie z zaleceniami EDPB (European Data Protection Board) i wytycznymi UODO moĹĽliwoĹ›Ä‡ odrzucenia cookies musi byÄ‡ rĂłwnie Ĺ‚atwo dostÄ™pna co ich akceptacja:

```
â”Śâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”
â”‚ đźŤŞ Ta strona uĹĽywa plikĂłw cookie                             â”‚
â”‚ [opis przeznaczenia]                 [Polityka prywatnoĹ›ci â†’]â”‚
â”śâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”¤
â”‚  [ Tylko niezbÄ™dne ]  [ Marketing ]  [ Akceptuj wszystko â–¶ ] â”‚
â”‚                       [ Dostosuj â–ľ ]                         â”‚
â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”
```

- **Tylko niezbÄ™dne** â€” jednym klikniÄ™ciem odmawia wszystkich kategorii opcjonalnych
- **Marketing** / **Akceptuj wszystko** â€” przyznaje peĹ‚nÄ… zgodÄ™
- **Dostosuj** â†’ Okno modalne z checkboxami per kategoria

---

## đźŤŞ Polityka plikĂłw cookie (wzorzec)

> PoniĹĽsza polityka stanowi wzorzec do dostosowania. TreĹ›Ä‡ naleĹĽy zmodyfikowaÄ‡ zgodnie ze specyfikÄ… wĹ‚asnej strony internetowej, stosowanymi narzÄ™dziami analitycznymi i reklamowymi oraz danymi Administratora.

PeĹ‚na, samodzielna wersja dostÄ™pna w pliku: **[docs/POLITYKA_COOKIES.md](docs/POLITYKA_COOKIES.md)**

---

### POLITYKA UĹ»YWANIA PLIKĂ“W COOKIE I TECHNOLOGII ĹšLEDZENIA

#### 1. Postanowienia ogĂłlne i architektura systemu zarzÄ…dzania zgodami

Niniejsza Strona internetowa stosuje pliki cookie i funkcjonalnie rĂłwnowaĹĽne technologie lokalnego przechowywania danych w celu: (a) zapewnienia prawidĹ‚owego technicznego funkcjonowania zasobu; (b) zbierania zagregowanych wskaĹşnikĂłw analitycznych; (c) realizacji strategii marketingu cyfrowego. ZarzÄ…dzanie parametrami prywatnoĹ›ci i preferencjami uĹĽytkownika koĹ„cowego odbywa siÄ™ za poĹ›rednictwem Platformy zarzÄ…dzania zgodami (Consent Management Platform â€” CMP) zintegrowanej z protokoĹ‚em Google Consent Mode v2.

System CMP zaprojektowany jest i dziaĹ‚a na bazie **architektury bezstanowej** (stateless). Ustawienia preferencji uĹĽytkownikĂłw nie sÄ… przetwarzane, agregowane ani przechowywane w serwerowych relacyjnych bazach danych Administratora. Status udzielonej lub odmĂłwionej zgody rejestrowany jest wyĹ‚Ä…cznie lokalnie, na koĹ„cowym urzÄ…dzeniu telekomunikacyjnym uĹĽytkownika, w postaci technicznego pliku cookie. RozwiÄ…zanie to zapewnia Ĺ›cisĹ‚e przestrzeganie zasady minimalizacji danych i wbudowanej ochrony prywatnoĹ›ci (Privacy by Design), zgodnie z art. 5 ust. 1 lit. c i art. 25 RODO.

#### 2. Podstawy prawno-normatywne przetwarzania danych

Przetwarzanie informacji z uĹĽyciem plikĂłw cookie opiera siÄ™ na:

- **RozporzÄ…dzeniu Parlamentu Europejskiego i Rady (UE) 2016/679** z 27 kwietnia 2016 r. (RODO);
- **Ustawie z dnia 12 lipca 2024 r. â€” Prawo komunikacji elektronicznej** (PKE, Dz. U. 2024 poz. 1221).

Zgodnie z art. 399 PKE, stosowanie Ĺ›ciĹ›le niezbÄ™dnych plikĂłw cookie **nie wymaga** uprzedniej zgody uĹĽytkownika. Aktywacja plikĂłw cookie kategorii analitycznych i marketingowych wymaga uprzedniej, dobrowolnej, konkretnej i Ĺ›wiadomej zgody (art. 6 ust. 1 lit. a RODO w zw. z art. 400 PKE).

#### 3. Klasyfikacja stosowanych plikĂłw cookie

| Kategoria | Cel | Podstawa prawna | Czas przechowywania | Wymagana zgoda |
|-----------|-----|-----------------|---------------------|----------------|
| **ĹšciĹ›le niezbÄ™dne** | DziaĹ‚anie strony, bezpieczeĹ„stwo, status CMP | Art. 399 PKE â€” interes prawnie uzasadniony | Sesja â€“ 365 dni | Nie |
| **Analityczne** | Statystyki, Google Analytics, optymalizacja | Art. 6(1)(a) RODO + art. 400 PKE | Do 24 miesiÄ™cy | **Tak â€” opt-in** |
| **Marketingowe** | Google Ads, Meta Pixel, remarketing | Art. 6(1)(a) RODO + art. 400 PKE | Do 24 miesiÄ™cy | **Tak â€” opt-in** |

**ĹšciĹ›le niezbÄ™dne:** NiezbÄ™dne do Ĺ›wiadczenia usĹ‚ugi elektronicznej ĹĽÄ…danej przez uĹĽytkownika. ObejmujÄ… pliki zapewniajÄ…ce bezpieczeĹ„stwo sesji, kierowanie ruchem i przechowywanie statusu zgody CMP. Nie podlegajÄ… dezaktywacji z poziomu interfejsu strony.

**Analityczne:** ZbierajÄ… zagregowane metryki statystyczne dotyczÄ…ce korzystania ze strony (m.in. Google Analytics). Nie sĹ‚uĹĽÄ… do bezpoĹ›redniej identyfikacji uĹĽytkownikĂłw. Aktywowane wyĹ‚Ä…cznie po udzieleniu wyraĹşnej zgody.

**Marketingowe:** UmoĹĽliwiajÄ… profilowanie i remarketing (m.in. Google Ads, Meta Pixel). PrzekazujÄ… parametry zgody zewnÄ™trznym dostawcom usĹ‚ug technologicznych. Aktywowane wyĹ‚Ä…cznie po udzieleniu wyraĹşnej zgody.

#### 4. Implementacja Google Consent Mode v2

Strona obsĹ‚uguje protokĂłĹ‚ Google Consent Mode v2 do dynamicznej kontroli zachowania tagĂłw Google w zaleĹĽnoĹ›ci od statusu zgody, przekazywanego przez parametry: `ad_user_data`, `ad_personalization`, `analytics_storage`, `ad_storage`.

Przy braku lub odwoĹ‚aniu zgody na pliki analityczne i marketingowe, menedĹĽer tagĂłw inicjuje status odmowy (`denied`). W trybie `denied`: system blokuje dostÄ™p do localStorage przeglÄ…darki, uniemoĹĽliwiajÄ…c odczyt/zapis plikĂłw cookie przez zewnÄ™trzne usĹ‚ugi reklamowe; komunikacja z zewnÄ™trznymi serwerami ograniczona jest wyĹ‚Ä…cznie do technicznych sygnaĹ‚Ăłw ping, pozbawionych identyfikatorĂłw marketingowych.

#### 5. ZarzÄ…dzanie preferencjami i okres przechowywania danych

UĹĽytkownik posiada peĹ‚ne prawo do udzielenia, modyfikacji lub odwoĹ‚ania zgody w **dowolnym momencie**, bez negatywnych konsekwencji (art. 7 ust. 3 RODO). OdwoĹ‚anie zgody ma skutek wyĹ‚Ä…cznie na przyszĹ‚oĹ›Ä‡.

**Interfejs:** StaĹ‚y interaktywny element (przycisk â€žUstawienia prywatnoĹ›ci" lub â€žZarzÄ…dzaj plikami cookie") w stopce kaĹĽdej strony serwisu umoĹĽliwia dostÄ™p do panelu CMP.

**Okres przechowywania:** Techniczny plik cookie `consent_preferences` przechowuje status zgody przez maksymalnie **12 miesiÄ™cy** od ostatniej modyfikacji. Po upĹ‚ywie tego okresu system inicjuje nowe zapytanie o zgodÄ™.

**Administracja kliencka:** UĹĽytkownik moĹĽe samodzielnie audytowaÄ‡ i usuwaÄ‡ pliki cookie przez ustawienia przeglÄ…darki.

#### 6. Podprzetwarzanie i transgraniczne przekazywanie danych

Po udzieleniu zgody na kategorie analityczne lub marketingowe, metadane mogÄ… byÄ‡ zbierane przez autoryzowanych dostawcĂłw technologicznych dziaĹ‚ajÄ…cych jako niezaleĹĽni administratorzy lub podmioty przetwarzajÄ…ce (m.in. Google Ireland Ltd, Meta Platforms Ireland Ltd).

Pierwotne przetwarzanie odbywa siÄ™ w EOG. Ewentualny transfer do krajĂłw trzecich (m.in. USA) legitymizowany jest przez: certyfikacjÄ™ w ramach EU-U.S. Data Privacy Framework (DPF) i/lub Standardowe Klauzule Umowne (SCC) zatwierdzone przez KomisjÄ™ EuropejskÄ….

---

## âť“ FAQ

**Czy wtyczka jest zgodna z polskim PKE 2024?**
Tak. Wtyczka implementuje model opt-in dla wszystkich kategorii opcjonalnych zgodnie z art. 400 PKE. Pliki niezbÄ™dne aktywne sÄ… bez zgody (art. 399 PKE).

**Czy tworzone sÄ… tabele w bazie danych?**
Nie. Preferencje przechowywane sÄ… w pliku cookie `consent_preferences` na urzÄ…dzeniu koĹ„cowym. Ustawienia wtyczki zapisywane sÄ… w `wp_options` przez standardowe WordPress Settings API.

**Jakie jÄ™zyki sÄ… obsĹ‚ugiwane?**
Angielski (en), Rosyjski (ru), UkraiĹ„ski (ua), Polski (pl). JÄ™zyk dobierany automatycznie na podstawie locale WordPress. KompatybilnoĹ›Ä‡ z WPML i Polylang.

**Czy wtyczka dziaĹ‚a z Google Tag Manager?**
Tak. GTM moĹĽe byÄ‡ Ĺ‚adowany przez wtyczkÄ™ (opcja w panelu admina) lub manualnie. GCMv2 inicjalizowany jest zawsze przed GTM (priorytet 0 vs 5 w `wp_head`).

**Jak zmieniÄ‡ czas waĹĽnoĹ›ci pliku cookie?**
UĹĽyj filtra `consent_mode_banner_config` i zmieĹ„ pole `expires` (wartoĹ›Ä‡ w dniach).

**Czy istnieje mechanizm odwoĹ‚ania zgody po zamkniÄ™ciu banera?**
Tak â€” po udzieleniu zgody w stopce strony pojawia siÄ™ staĹ‚y przycisk (ikona ciasteczka). KlikniÄ™cie go ponownie wyĹ›wietla baner i pozwala zmieniÄ‡ lub cofnÄ…Ä‡ zgodÄ™.

**Czy wtyczka jest dostÄ™pna dla uĹĽytkownikĂłw z niepeĹ‚nosprawnoĹ›ciami?**
Baner i `<dialog>` zawierajÄ… role ARIA (`role="dialog"`, `aria-labelledby`, `aria-describedby`, `aria-expanded`), obsĹ‚ugÄ™ klawiatury (Escape zamyka modal, focus trap natywnego `<dialog>`), kontrastowe obramowania `:focus-visible`.

**Jak przetestowaÄ‡ integracjÄ™ z GCMv2?**
1. OtwĂłrz stronÄ™ w trybie incognito
2. W konsoli DevTools sprawdĹş `window.dataLayer` â€” powinna byÄ‡ tablica z domyĹ›lnym stanem `denied`
3. Kliknij â€žAkceptuj wszystko" â€” sprawdĹş `window.dataLayer` dla `consent update`
4. Opcjonalnie: uĹĽyj Google Tag Assistant lub trybu podglÄ…du GTM

---

## đź“ť Changelog

PeĹ‚na historia zmian dostÄ™pna w pliku [CHANGELOG.md](CHANGELOG.md) â€” format zgodny z [Keep a Changelog](https://keepachangelog.com/pl/1.0.0/), wersjonowanie zgodne z [Semantic Versioning](https://semver.org/lang/pl/).

---

## đź“„ Licencja

[GPL v2 lub nowsza](https://www.gnu.org/licenses/gpl-2.0.html) â€” zgodnie z wymogami ekosystemu WordPress.

```
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2,
as published by the Free Software Foundation.
```

---

*Dokumentacja aktualizowana: 2026-02-26 Â· Wersja wtyczki: 1.1.0 Â· Autor: Your Name*