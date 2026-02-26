# Polityka używania plików cookie i technologii śledzenia

> **Dokument prawny – wzorzec do dostosowania.**
> Niniejszy dokument stanowi przykładową treść polityki plików cookie dla stron
> korzystających z wtyczki Universal Consent Mode (GCMv2). Administrator strony
> zobowiązany jest dostosować treść do specyfiki własnej działalności, stosowanych
> narzędzi i danych kontaktowych Administratora danych.

---

## 1. Postanowienia ogólne i architektura systemu zarządzania zgodami

Niniejsza Strona internetowa stosuje pliki cookie i funkcjonalnie równoważne technologie lokalnego przechowywania danych w celu: (a) zapewnienia prawidłowego technicznego funkcjonowania zasobu; (b) zbierania zagregowanych wskaźników analitycznych; (c) realizacji strategii marketingu cyfrowego. Zarządzanie parametrami prywatności i preferencjami użytkownika końcowego odbywa się za pośrednictwem Platformy zarządzania zgodami (Consent Management Platform – CMP), zintegrowanej z protokołem Google Consent Mode v2.

System CMP zaprojektowany jest i działa na bazie **architektury bezstanowej** (stateless). Ustawienia preferencji użytkowników nie są przetwarzane, agregowane ani przechowywane w serwerowych relacyjnych bazach danych Administratora. Status udzielonej lub odmówionej zgody rejestrowany jest wyłącznie lokalnie, na końcowym urządzeniu telekomunikacyjnym użytkownika, w postaci lekkiego technicznego pliku cookie. Rozwiązanie to zapewnia ścisłe przestrzeganie zasady minimalizacji danych i wbudowanej ochrony prywatności (Privacy by Design), zgodnie z art. 5 ust. 1 lit. c i art. 25 Rozporządzenia (UE) 2016/679 (RODO).

## 2. Podstawy prawno-normatywne przetwarzania danych

Przetwarzanie informacji realizowane z użyciem plików cookie i powiązanych technologii opiera się na ścisłym przestrzeganiu przepisów następujących aktów prawnych:

- **Rozporządzenie Parlamentu Europejskiego i Rady (UE) 2016/679** z dnia 27 kwietnia 2016 r. o ochronie osób fizycznych w związku z przetwarzaniem danych osobowych i o swobodnym przepływie takich danych (RODO);
- **Ustawa z dnia 12 lipca 2024 r. – Prawo komunikacji elektronicznej** (PKE, Dz. U. 2024 poz. 1221).

Zgodnie z bezwzględnie obowiązującymi normami **art. 399 PKE**, stosowanie ściśle niezbędnych plików cookie zapewniających transmisję sygnału w sieci telekomunikacyjnej **nie wymaga** uprzedniej zgody użytkownika końcowego.

Iniekcja skryptów i aktywacja plików cookie innych kategorii funkcjonalnych (analitycznych i marketingowych) opiera się wyłącznie na uprzedniej, dobrowolnej, konkretnej i świadomej zgodzie użytkownika, udzielanej zgodnie z **art. 6 ust. 1 lit. a RODO** w związku z **art. 400 PKE**.

## 3. Klasyfikacja stosowanych plików cookie

### 3.1 Ściśle niezbędne (Strictly Necessary)

Niezbędne do świadczenia usługi elektronicznej żądanej przez użytkownika. Obejmują pliki zapewniające bezpieczeństwo sesji, kierowanie ruchem i przechowywanie statusu zgody CMP. Nie podlegają dezaktywacji z poziomu interfejsu strony.

**Podstawa prawna:** art. 399 PKE (interes prawnie uzasadniony – niezbędność techniczna).  
**Okres przechowywania:** sesja / do 365 dni (plik CMP).  
**Możliwość wyłączenia:** Nie – kategoria nie podlega dezaktywacji z poziomu interfejsu Strony.

### 3.2 Analityczne (Analytics)

Narzędzia zbierające wskaźniki statystyczne dotyczące korzystania ze Strony (m.in. za pomocą serwisów analityki internetowej, w tym Google Analytics). Stosowane do:
- ilościowej oceny interakcji użytkowników z interfejsem zasobu,
- wykrywania błędów technicznych i optymalizacji wydajności,
- rozumienia sposobu nawigacji po Stronie.

Proces zbierania danych nie jest ukierunkowany na bezpośrednią identyfikację konkretnych osób fizycznych.

**Podstawa prawna:** art. 6 ust. 1 lit. a RODO + art. 400 PKE – zgoda.  
**Okres przechowywania:** do 24 miesięcy.  
**Możliwość wyłączenia:** Tak – w panelu preferencji CMP (opt-in / opt-out w dowolnym momencie).

### 3.3 Marketingowe (Marketing)

Narzędzia profilowania i śledzenia, stosowane do:
- oceny wskaźnika zwrotu z inwestycji (ROI) kampanii reklamowych,
- personalizacji komunikatów handlowych i ofert,
- realizacji remarketingu i targetowania reklam (m.in. Google Ads, Meta Pixel).

Funkcjonalność obejmuje przekazywanie parametrów zgody na użycie danych do targetowania i remarketingu zewnętrznym dostawcom usług technologicznych.

**Podstawa prawna:** art. 6 ust. 1 lit. a RODO + art. 400 PKE – zgoda.  
**Okres przechowywania:** do 24 miesięcy.  
**Możliwość wyłączenia:** Tak – w panelu preferencji CMP (opt-in / opt-out w dowolnym momencie).

## 4. Techniczna implementacja Google Consent Mode v2

Infrastruktura Strony obsługuje integrację z protokołem **Google Consent Mode v2** (GCMv2), umożliwiającą dynamiczną kontrolę i trasowanie zachowania tagów Google w zależności od statusu zgody użytkownika, przekazywanego przez parametry:

| Parametr | Kategoria | Opis |
|----------|-----------|------|
| `ad_storage` | Marketingowe | Przechowywanie danych reklamowych |
| `ad_user_data` | Marketingowe | Użycie danych użytkownika dla reklam |
| `ad_personalization` | Marketingowe | Personalizacja reklam |
| `analytics_storage` | Analityczne | Przechowywanie danych analitycznych |
| `functionality_storage` | Niezbędne | Funkcjonalność strony |
| `personalization_storage` | Opcjonalne | Personalizacja treści |
| `security_storage` | Niezbędne | Bezpieczeństwo i ochrona przed nadużyciami |

### Tryb odmowy (denied)

W przypadku braku zgody lub jej odwołania w odniesieniu do kategorii analitycznych lub marketingowych:
- Menedżer tagów inicjuje status technicznej odmowy (`denied`);
- System blokuje dostęp do localStorage przeglądarki – uniemożliwia odczyt i zapis plików cookie przez zewnętrzne usługi reklamowe;
- Komunikacja z zewnętrznymi serwerami ograniczona wyłącznie do podstawowych sygnałów technicznych (ping), pozbawionych identyfikatorów marketingowych i danych umożliwiających profilowanie poszczególnych użytkowników.

## 5. Zarządzanie preferencjami i okres przechowywania danych

### 5.1 Prawo do zarządzania zgodami

Użytkownik końcowy posiada **niezbywalne prawo** do:
- udzielenia zgody na wybrane lub wszystkie kategorie plików cookie,
- zmiany specyfikacji udzielonej zgody w dowolnym momencie,
- całkowitego odwołania zgody bez negatywnych konsekwencji dla korzystania ze Strony.

Odwołanie zgody ma skutek **wyłącznie na przyszłość** i nie podważa zgodności z prawem przetwarzania danych dokonanego na podstawie zgody przed jej odwołaniem (art. 7 ust. 3 RODO).

### 5.2 Interfejs zarządzania

Bezpośredni dostęp do panelu konfiguracji CMP realizowany jest za pośrednictwem stałego interaktywnego elementu (przycisk / link „**Ustawienia prywatności**" lub „**Zarządzaj plikami cookie**") umieszczonego w stopce (footer) każdej strony Serwisu.

### 5.3 Okres przechowywania (Data Retention)

Techniczny plik cookie `consent_preferences`, dokumentujący status zgody na urządzeniu końcowym użytkownika, generowany jest z terminem ważności nieprzekraczającym **12 miesięcy** od chwili ostatniej modyfikacji preferencji. Po upływie tego okresu, lub w wyniku wymuszonego czyszczenia pamięci podręcznej przeglądarki, system inicjuje nowe zapytanie o zgodę.

### 5.4 Administracja kliencka

Użytkownik zachowuje prawo do samodzielnego audytu i usuwania plików cookie za pomocą wbudowanych mechanizmów bezpieczeństwa oprogramowania przeglądarki internetowej. Instrukcje dostępne są w dokumentacji stosowanej przeglądarki:
- [Google Chrome](https://support.google.com/chrome/answer/95647)
- [Mozilla Firefox](https://support.mozilla.org/pl/kb/usuwanie-ciasteczek)
- [Apple Safari](https://support.apple.com/pl-pl/guide/safari/sfri11471)
- [Microsoft Edge](https://support.microsoft.com/pl-pl/microsoft-edge/usuwanie-plik%C3%B3w-cookie-w-przegl%C4%85darce-microsoft-edge)

## 6. Podprzetwarzanie i transgraniczne przekazywanie danych

W przypadku udzielenia zgody na aktywację skryptów kategorii analitycznej lub marketingowej, metadane mogą być zbierane i przetwarzane przez upoważnionych dostawców usług technologicznych, działających w charakterze niezależnych administratorów lub podmiotów przetwarzających, w szczególności:

| Dostawca | Rola | Obszar przetwarzania |
|----------|------|----------------------|
| Google Ireland Ltd (Alphabet Inc.) | Niezależny administrator / podmiot przetwarzający | EOG + USA (DPF) |
| Meta Platforms Ireland Ltd (Meta Inc.) | Niezależny administrator / podmiot przetwarzający | EOG + USA (DPF + SCC) |

Pierwotna lokalizacja i przetwarzanie tych danych realizowane są na terenie **Europejskiego Obszaru Gospodarczego (EOG)**. Dalsze trasowanie i transgraniczne przekazywanie danych do krajów trzecich (w szczególności Stanów Zjednoczonych Ameryki) legitymizowane jest przez:

- **EU-U.S. Data Privacy Framework (DPF)** – certyfikacja strony przyjmującej uznana przez Komisję Europejską decyzją z 10 lipca 2023 r.; lub
- **Standardowe Klauzule Umowne (SCC)** – zatwierdzone przez Komisję Europejską decyzją z 4 czerwca 2021 r. (Dz. Urz. UE L 199/31).

---

## Dane kontaktowe Administratora

*[Uzupełnij danymi Administratora danych – pełna nazwa, adres, e-mail DPO/IOD]*

**Administrator danych osobowych:** [Nazwa firmy/osoby]  
**Adres:** [Adres korespondencyjny]  
**E-mail do kontaktu w sprawach prywatności:** [privacy@example.com]  
**Inspektor Ochrony Danych (IOD/DPO):** [Imię Nazwisko / e-mail, jeśli dotyczy]

---

*Dokument ostatnio aktualizowany: 2026-02-26*  
*Wersja: 1.0*  
*Zgodność: RODO art. 13/14 · PKE 2024 art. 399-400 · GCMv2*