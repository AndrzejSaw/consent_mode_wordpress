# Universal Consent Mode (GCMv2)

[![WordPress](https://img.shields.io/badge/WordPress-6.2%2B-blue)](https://wordpress.org/)
[![PHP](https://img.shields.io/badge/PHP-8.1%2B-purple)](https://www.php.net/)
[![License](https://img.shields.io/badge/License-GPL%20v2-green)](LICENSE.txt)
[![Google Consent Mode v2](https://img.shields.io/badge/Google%20Consent%20Mode-v2-orange)](https://developers.google.com/tag-platform/security/guides/consent)

**Uniwersalna wtyczka WordPress dla Google Consent Mode v2** z obsługą wielu języków (EN, RU, PL) i zgodnością z RODO dla UE/EOG, Polski, Ukrainy, Białorusi i Rosji.

---

## 📋 Spis treści

- [Funkcje](#-funkcje)
- [Wymagania](#-wymagania)
- [Instalacja](#-instalacja)
- [Szybki start](#-szybki-start)
- [Konfiguracja](#-konfiguracja)
- [Użycie](#-użycie)
- [Struktura wtyczki](#-struktura-wtyczki)
- [API i Hooki](#-api-i-hooki)
- [FAQ](#-faq)
- [Wsparcie](#-wsparcie)

---

## 🎯 Funkcje

### Uniwersalność i wielojęzyczność
- ✅ **Wsparcie wielojęzyczne**: Wbudowane tłumaczenia dla języka angielskiego, rosyjskiego i polskiego.
- ✅ **Tryb ścisły (Strict Mode)**: Automatycznie stosuje ścisłe zasady zgody RODO dla użytkowników w **UE/EOG, Polsce, Ukrainie, Białorusi i Rosji**.
- ✅ **Konfigurowalna treść**: Edycja tytułów banerów, opisów i linków do polityki prywatności dla każdego języka za pośrednictwem panelu administracyjnego.

### Google Consent Mode v2
- ✅ **Pełna integracja** z najnowszym API Consent Mode.
- ✅ **Domyślny stan zgody**: Wszystkie kategorie domyślnie odrzucone w regionach objętych restrykcjami (z wyjątkiem security_storage).
- ✅ **Aktualizacja zgody**: Dynamiczne aktualizacje po wyborze użytkownika.
- ✅ **7 kategorii zgody**: analytics_storage, ad_storage, ad_user_data, ad_personalization, functionality_storage, personalization_storage, security_storage.

### Menedżer tagów Google (GTM)
- ✅ **Automatyczne ładowanie GTM** z uwzględnieniem zgody.
- ✅ **Priorytet ładowania**: Consent Mode (priorytet 0) → GTM (priorytet 5).
- ✅ **Obsługa Noscript** dla użytkowników bez JavaScript.
- ✅ **Wsparcie środowisk**: gtm_auth, gtm_preview, gtm_cookies_win.
- ✅ **Ochrona DataLayer**: Zapobiega duplikacji.

### Strażnik Skryptów (Script Guard)
- ✅ **Blokowanie skryptów śledzących** do momentu uzyskania zgody.
- ✅ **Kategorie**: analityka, reklamy, funkcjonalne.
- ✅ **Automatyczna reaktywacja** po uzyskaniu zgody.
- ✅ **Zachowanie atrybutów**: async, defer, crossorigin, integrity, nonce.
- ✅ **Obsługa skryptów zewnętrznych i inline**.

### Baner Zgody
- ✅ **Responsywny design** dla wszystkich urządzeń.
- ✅ **Trzy przyciski**: Akceptuj wszystko, Odrzuć wszystko, Dostosuj.
- ✅ **Mechanizm odwołania**: Pływający przycisk do ponownego otwarcia ustawień (zgodność z RODO).
- ✅ **Szczegółowa konfiguracja** za pomocą pól wyboru.
- ✅ **Zarządzanie plikami cookie** z konfigurowalnym czasem wygasania.
- ✅ **Zapisywanie AJAX** zgody.

### Geolokalizacja
- ✅ **Nagłówki CloudFlare** (HTTP_CF_IPCOUNTRY).
- ✅ **Buforowanie** wykrywania kraju (24 godziny).
- ✅ **Rozszerzalność**: Miejsce na usługi IP.

### Panel Administratora
- ✅ Integracja z **Settings API**.
- ✅ **Konfiguracja GTM** (identyfikator kontenera, włącz/wyłącz).
- ✅ **Mapowanie kategorii** (listy CSV uchwytów skryptów).
- ✅ **Ustawienia treści wielojęzycznych**.
- ✅ **Walidacja i sanityzacja**.

---

## 💻 Wymagania

- **WordPress**: 6.2 lub nowszy
- **PHP**: 8.1 lub nowszy
- **Composer**: Do rozwoju (opcjonalnie)

---

## 📦 Instalacja

### Metoda 1: Przez panel administratora WordPress

1. Pobierz archiwum wtyczki.
2. Przejdź do **Wtyczki → Dodaj nową → Wyślij wtyczkę na serwer**.
3. Prześlij plik ZIP.
4. Kliknij **Włącz**.

### Metoda 2: Przez FTP/SSH

```bash
# Prześlij folder ru-consent-mode do wp-content/plugins/
cd /path/to/wordpress/wp-content/plugins/
unzip universal-consent-mode.zip

# Jeśli musisz zainstalować zależności deweloperskie:
cd ru-consent-mode
composer install --no-dev
```

---

## 🚀 Szybki start

### Krok 1: Aktywacja
Po aktywacji wtyczka automatycznie:
- Inicjuje Google Consent Mode v2 (domyślnie odrzucone w regionach restrykcyjnych).
- Dodaje baner zgody na stronie.
- Blokuje skrypty śledzące do momentu uzyskania zgody.

### Krok 2: Konfiguracja GTM (Opcjonalnie)

Przejdź do **Ustawienia → Universal Consent Mode**:

```
Google Tag Manager Settings
├── Enable GTM Loader: ✓
└── GTM Container ID: GTM-XXXXXXX
```

### Krok 3: Konfiguracja kategorii skryptów

W sekcji **Script Guard Settings** podaj uchwyty (handles) skryptów:

```
Analytics Scripts:
google-analytics, ga4, clarity, matomo

Advertising Scripts:
googletag, adsbygoogle, fb-pixel, twitter-pixel

Functional Scripts:
youtube-api, vimeo-player, google-maps
```

### Krok 4: Treść wielojęzyczna

W sekcji **Multilingual Content** możesz dostosować tekst banera dla języka angielskiego, rosyjskiego i polskiego. Jeśli pozostawisz puste, zostaną użyte domyślne tłumaczenia.

### Krok 5: Testowanie

1. Otwórz stronę w trybie incognito.
2. Zobaczysz baner zgody.
3. Otwórz DevTools → Console.
4. Sprawdź obecność `dataLayer` i `gtag()`.
5. Kliknij "Akceptuj wszystko" - skrypty zostaną aktywowane.

---

## ⚙️ Konfiguracja

### Główne parametry

Wtyczka zapisuje ustawienia w opcji `ru_consent_mode_settings`:

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

### Konfiguracja programowa

Dodaj do `functions.php`:

```php
// Włącz GTM
add_filter('ru_consent_mode_gtm_enabled', '__return_true');

// Ustaw ID kontenera GTM
add_filter('ru_consent_mode_gtm_container_id', function() {
    return 'GTM-XXXXXXX';
});

// Dodaj skrypty do kategorii analityki
add_filter('ru_consent_mode_categories_map', function($map) {
    $map['analytics'] = 'ga4, clarity, custom-analytics';
    return $map;
});
```

---

## 📖 Użycie

### Rejestracja skryptów z kategoriami

Podczas dodawania skryptów używaj uchwytów z ustawień:

```php
// Google Analytics 4
wp_enqueue_script(
    'ga4', // uchwyt z categories_map['analytics']
    'https://www.googletagmanager.com/gtag/js?id=G-XXXXXXXXXX',
    [],
    null,
    true
);

// Facebook Pixel
wp_enqueue_script(
    'fb-pixel', // uchwyt z categories_map['ads']
    'https://connect.facebook.net/en_US/fbevents.js',
    [],
    null,
    true
);
```

Wtyczka automatycznie:
1. Zablokuje skrypt (`type="text/plain"`).
2. Doda atrybut `data-rcm-consent="analytics"`.
3. Reaktywuje go po uzyskaniu zgody.

### Obsługa zgody w JavaScript

```javascript
// Sprawdź obecną zgodę
const consent = RUConsentBanner.getCookie('ru_consent_mode');
const data = JSON.parse(consent);

if (data.analytics_storage === 'granted') {
    // Zainicjuj analitykę
}

// Nasłuchuj aktualizacji zgody
document.addEventListener('ruConsentUpdated', function(event) {
    console.log('Nowa zgoda:', event.detail);
    
    if (event.detail.ad_storage === 'granted') {
        // Załaduj skrypty reklamowe
    }
});
```

---

## 🏗️ Struktura wtyczki

```
ru-consent-mode/
├── ru-consent-mode.php          # Główny plik wtyczki
├── uninstall.php                # Czyszczenie przy odinstalowaniu
├── composer.json                # Konfiguracja Composer
├── README.md                    # Dokumentacja (Angielski)
├── README_PL.md                 # Dokumentacja (Polski)
├── CHANGELOG.md                 # Dziennik zmian
├── readme.txt                   # Readme dla WordPress.org
│
├── src/                         # Klasy PHP (PSR-4)
│   ├── Admin/
│   │   └── Admin.php           # Panel administratora i ustawienia
│   ├── Consent/
│   │   ├── Bootstrap.php       # Inicjalizacja Google Consent Mode v2
│   │   └── Consent.php         # Zarządzanie zgodą
│   ├── Front/
│   │   ├── Front.php           # Koordynacja frontendowa
│   │   └── ScriptGuard.php     # Blokowanie skryptów
│   ├── Geo/
│   │   └── Geo.php             # Geolokalizacja
│   └── ...
│
├── assets/
│   ├── css/
│   │   └── banner.css          # Style banera
│   └── js/
│       └── banner.js           # JavaScript banera
│
└── languages/                   # Tłumaczenia (i18n)
```

---

## ❓ FAQ

### Jak działa blokowanie skryptów?
Wtyczka używa filtra `script_loader_tag` do modyfikacji HTML. Skrypty są zmieniane na `type="text/plain"` do momentu uzyskania zgody, a następnie zmieniane z powrotem na `text/javascript` i wykonywane.

### Które skrypty są blokowane?
Tylko te, których uchwyty są określone w ustawieniach `categories_map`. Inne ładują się normalnie.

### Czy to działa bez GTM?
Tak! Google Consent Mode v2 działa niezależnie od GTM. GTM to opcjonalna integracja.

### Czy zgoda jest zapisywana w bazie danych?
W obecnej wersji zgoda jest przechowywana tylko w pliku cookie. Planowane jest logowanie do bazy danych.

---

## 🤝 Wsparcie

### Zgłoś błąd
Utwórz zgłoszenie na GitHub.

### Dokumentacja
- [Google Consent Mode v2](https://developers.google.com/tag-platform/security/guides/consent)
- [Integracja GTM](https://developers.google.com/tag-platform/tag-manager/web)

---

## 📄 Licencja

Ta wtyczka jest rozpowszechniana na licencji **GPL v2 lub nowszej**.

---

**Stworzone z ❤️ dla społeczności WordPress**
