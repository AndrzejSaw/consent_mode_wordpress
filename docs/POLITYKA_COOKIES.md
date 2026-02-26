# Polityka uĹĽywania plikĂłw cookie i technologii Ĺ›ledzenia

> **Dokument prawny â€” wzorzec do dostosowania.**
> Niniejszy dokument stanowi przykĹ‚adowÄ… treĹ›Ä‡ polityki plikĂłw cookie dla stron
> korzystajÄ…cych z wtyczki Universal Consent Mode (GCMv2). Administrator strony
> zobowiÄ…zany jest dostosowaÄ‡ treĹ›Ä‡ do specyfiki wĹ‚asnej dziaĹ‚alnoĹ›ci, stosowanych
> narzÄ™dzi i danych kontaktowych Administratora danych.

---

## 1. Postanowienia ogĂłlne i architektura systemu zarzÄ…dzania zgodami

Niniejsza Strona internetowa stosuje pliki cookie i funkcjonalnie rĂłwnowaĹĽne technologie lokalnego przechowywania danych w celu: (a) zapewnienia prawidĹ‚owego technicznego funkcjonowania zasobu; (b) zbierania zagregowanych wskaĹşnikĂłw analitycznych; (c) realizacji strategii marketingu cyfrowego. ZarzÄ…dzanie parametrami prywatnoĹ›ci i preferencjami uĹĽytkownika koĹ„cowego odbywa siÄ™ za poĹ›rednictwem Platformy zarzÄ…dzania zgodami (Consent Management Platform â€” CMP), zintegrowanej z protokoĹ‚em Google Consent Mode v2.

System CMP zaprojektowany jest i dziaĹ‚a na bazie **architektury bezstanowej** (stateless). Ustawienia preferencji uĹĽytkownikĂłw nie sÄ… przetwarzane, agregowane ani przechowywane w serwerowych relacyjnych bazach danych Administratora. Status udzielonej lub odmĂłwionej zgody rejestrowany jest wyĹ‚Ä…cznie lokalnie, na koĹ„cowym urzÄ…dzeniu telekomunikacyjnym uĹĽytkownika, w postaci lekkiego technicznego pliku cookie. RozwiÄ…zanie to zapewnia Ĺ›cisĹ‚e przestrzeganie zasady minimalizacji danych i wbudowanej ochrony prywatnoĹ›ci (Privacy by Design), zgodnie z art. 5 ust. 1 lit. c i art. 25 RozporzÄ…dzenia (UE) 2016/679 (RODO).

## 2. Podstawy prawno-normatywne przetwarzania danych

Przetwarzanie informacji realizowane z uĹĽyciem plikĂłw cookie i powiÄ…zanych technologii opiera siÄ™ na Ĺ›cisĹ‚ym przestrzeganiu przepisĂłw nastÄ™pujÄ…cych aktĂłw prawnych:

- **RozporzÄ…dzenie Parlamentu Europejskiego i Rady (UE) 2016/679** z dnia 27 kwietnia 2016 r. o ochronie osĂłb fizycznych w zwiÄ…zku z przetwarzaniem danych osobowych i o swobodnym przepĹ‚ywie takich danych (RODO);
- **Ustawa z dnia 12 lipca 2024 r. â€” Prawo komunikacji elektronicznej** (PKE, Dz. U. 2024 poz. 1221).

Zgodnie z bezwzglÄ™dnie obowiÄ…zujÄ…cymi normami **art. 399 PKE**, stosowanie Ĺ›ciĹ›le niezbÄ™dnych plikĂłw cookie zapewniajÄ…cych transmisjÄ™ sygnaĹ‚u w sieci telekomunikacyjnej **nie wymaga** uprzedniej zgody uĹĽytkownika koĹ„cowego.

Iniekcja skryptĂłw i aktywacja plikĂłw cookie innych kategorii funkcjonalnych (analitycznych i marketingowych) opiera siÄ™ wyĹ‚Ä…cznie na uprzedniej, dobrowolnej, konkretnej i Ĺ›wiadomej zgodzie uĹĽytkownika, udzielanej zgodnie z **art. 6 ust. 1 lit. a RODO** w zwiÄ…zku z **art. 400 PKE**.

## 3. Klasyfikacja stosowanych plikĂłw cookie

### 3.1 ĹšciĹ›le niezbÄ™dne (Strictly Necessary)

Pliki technicznie i funkcjonalnie obowiÄ…zkowe dla Ĺ›wiadczenia usĹ‚ug informacyjnych ĹĽÄ…danych przez uĹĽytkownika. Kategoria ta obejmuje technologie zapewniajÄ…ce:
- bezpieczeĹ„stwo kryptograficzne poĹ‚Ä…czenia HTTPS,
- prawidĹ‚owe kierowanie ruchem sieciowym,
- przechowywanie statusu zgody uĹĽytkownika w interfejsie CMP (plik `consent_preferences`).

**Podstawa prawna:** art. 399 PKE (interes prawnie uzasadniony â€” niezbÄ™dnoĹ›Ä‡ techniczna).  
**Okres przechowywania:** sesja / do 365 dni (plik CMP).  
**MoĹĽliwoĹ›Ä‡ wyĹ‚Ä…czenia:** Nie â€” kategoria nie podlega dezaktywacji z poziomu interfejsu Strony.

### 3.2 Analityczne (Analytics)

NarzÄ™dzia zbierajÄ…ce wskaĹşniki statystyczne dotyczÄ…ce korzystania ze Strony (m.in. za pomocÄ… serwisĂłw analityki internetowej, w tym Google Analytics). Stosowane do:
- iloĹ›ciowej oceny interakcji uĹĽytkownikĂłw z interfejsem zasobu,
- wykrywania bĹ‚Ä™dĂłw technicznych i optymalizacji wydajnoĹ›ci,
- rozumienia sposobu nawigacji po Stronie.

Proces zbierania danych nie jest ukierunkowany na bezpoĹ›redniÄ… identyfikacjÄ™ konkretnych osĂłb fizycznych.

**Podstawa prawna:** art. 6 ust. 1 lit. a RODO + art. 400 PKE â€” zgoda.  
**Okres przechowywania:** do 24 miesiÄ™cy.  
**MoĹĽliwoĹ›Ä‡ wyĹ‚Ä…czenia:** Tak â€” w panelu preferencji CMP (opt-in / opt-out w dowolnym momencie).

### 3.3 Marketingowe (Marketing)

NarzÄ™dzia profilowania i Ĺ›ledzenia, stosowane do:
- oceny wskaĹşnika zwrotu z inwestycji (ROI) kampanii reklamowych,
- personalizacji komunikatĂłw handlowych i ofert,
- realizacji remarketingu i targetowania reklam (m.in. Google Ads, Meta Pixel).

FunkcjonalnoĹ›Ä‡ obejmuje przekazywanie parametrĂłw zgody na uĹĽycie danych do targetowania i remarketingu zewnÄ™trznym dostawcom usĹ‚ug technologicznych.

**Podstawa prawna:** art. 6 ust. 1 lit. a RODO + art. 400 PKE â€” zgoda.  
**Okres przechowywania:** do 24 miesiÄ™cy.  
**MoĹĽliwoĹ›Ä‡ wyĹ‚Ä…czenia:** Tak â€” w panelu preferencji CMP (opt-in / opt-out w dowolnym momencie).

## 4. Techniczna implementacja Google Consent Mode v2

Infrastruktura Strony obsĹ‚uguje integracjÄ™ z protokoĹ‚em **Google Consent Mode v2** (GCMv2), umoĹĽliwiajÄ…cÄ… dynamicznÄ… kontrolÄ™ i trasowanie zachowania tagĂłw Google w zaleĹĽnoĹ›ci od statusu zgody uĹĽytkownika, przekazywanego przez parametry:

| Parametr | Kategoria | Opis |
|----------|-----------|------|
| `ad_storage` | Marketingowe | Przechowywanie danych reklamowych |
| `ad_user_data` | Marketingowe | UĹĽycie danych uĹĽytkownika dla reklam |
| `ad_personalization` | Marketingowe | Personalizacja reklam |
| `analytics_storage` | Analityczne | Przechowywanie danych analitycznych |
| `functionality_storage` | NiezbÄ™dne | FunkcjonalnoĹ›Ä‡ strony |
| `personalization_storage` | Opcjonalne | Personalizacja treĹ›ci |
| `security_storage` | NiezbÄ™dne | BezpieczeĹ„stwo i ochrona przed naduĹĽyciami |

### Tryb odmowy (denied)

W przypadku braku zgody lub jej odwoĹ‚ania w odniesieniu do kategorii analitycznych lub marketingowych:
- MenedĹĽer tagĂłw inicjuje status technicznej odmowy (`denied`);
- System blokuje dostÄ™p do localStorage przeglÄ…darki â€” uniemoĹĽliwia odczyt i zapis plikĂłw cookie przez zewnÄ™trzne usĹ‚ugi reklamowe;
- Komunikacja z zewnÄ™trznymi serwerami ograniczona wyĹ‚Ä…cznie do podstawowych sygnaĹ‚Ăłw technicznych (ping), pozbawionych identyfikatorĂłw marketingowych i danych umoĹĽliwiajÄ…cych profilowanie poszczegĂłlnych uĹĽytkownikĂłw.

## 5. ZarzÄ…dzanie preferencjami i okres przechowywania danych

### 5.1 Prawo do zarzÄ…dzania zgodami

UĹĽytkownik koĹ„cowy posiada **niezbywalne prawo** do:
- udzielenia zgody na wybrane lub wszystkie kategorie plikĂłw cookie,
- zmiany specyfikacji udzielonej zgody w dowolnym momencie,
- caĹ‚kowitego odwoĹ‚ania zgody bez negatywnych konsekwencji dla korzystania ze Strony.

OdwoĹ‚anie zgody ma skutek **wyĹ‚Ä…cznie na przyszĹ‚oĹ›Ä‡** i nie podwaĹĽa zgodnoĹ›ci z prawem przetwarzania danych dokonanego na podstawie zgody przed jej odwoĹ‚aniem (art. 7 ust. 3 RODO).

### 5.2 Interfejs zarzÄ…dzania

BezpoĹ›redni dostÄ™p do panelu konfiguracji CMP realizowany jest za poĹ›rednictwem staĹ‚ego interaktywnego elementu (przycisk / link â€ž**Ustawienia prywatnoĹ›ci**" lub â€ž**ZarzÄ…dzaj plikami cookie**") umieszczonego w stopce (footer) kaĹĽdej strony Serwisu.

### 5.3 Okres przechowywania (Data Retention)

Techniczny plik cookie `consent_preferences`, dokumentujÄ…cy status zgody na urzÄ…dzeniu koĹ„cowym uĹĽytkownika, generowany jest z terminem waĹĽnoĹ›ci nieprzekraczajÄ…cym **12 miesiÄ™cy** od chwili ostatniej modyfikacji preferencji. Po upĹ‚ywie tego okresu, lub w wyniku wymuszonego czyszczenia pamiÄ™ci podrÄ™cznej przeglÄ…darki, system inicjuje nowe zapytanie o zgodÄ™.

### 5.4 Administracja kliencka

UĹĽytkownik zachowuje prawo do samodzielnego audytu i usuwania plikĂłw cookie za pomocÄ… wbudowanych mechanizmĂłw bezpieczeĹ„stwa oprogramowania przeglÄ…darki internetowej. Instrukcje dostÄ™pne sÄ… w dokumentacji stosowanej przeglÄ…darki:
- [Google Chrome](https://support.google.com/chrome/answer/95647)
- [Mozilla Firefox](https://support.mozilla.org/pl/kb/usuwanie-ciasteczek)
- [Apple Safari](https://support.apple.com/pl-pl/guide/safari/sfri11471)
- [Microsoft Edge](https://support.microsoft.com/pl-pl/microsoft-edge/usuwanie-plik%C3%B3w-cookie-w-przegl%C4%85darce-microsoft-edge)

## 6. Podprzetwarzanie i transgraniczne przekazywanie danych

W przypadku udzielenia zgody na aktywacjÄ™ skryptĂłw kategorii analitycznej lub marketingowej, metadane mogÄ… byÄ‡ zbierane i przetwarzane przez upowaĹĽnionych dostawcĂłw usĹ‚ug technologicznych, dziaĹ‚ajÄ…cych w charakterze niezaleĹĽnych administratorĂłw lub podmiotĂłw przetwarzajÄ…cych, w szczegĂłlnoĹ›ci:

| Dostawca | Rola | Obszar przetwarzania |
|----------|------|----------------------|
| Google Ireland Ltd (Alphabet Inc.) | NiezaleĹĽny administrator / podmiot przetwarzajÄ…cy | EOG + USA (DPF) |
| Meta Platforms Ireland Ltd (Meta Inc.) | NiezaleĹĽny administrator / podmiot przetwarzajÄ…cy | EOG + USA (DPF + SCC) |

Pierwotna lokalizacja i przetwarzanie tych danych realizowane sÄ… na terenie **Europejskiego Obszaru Gospodarczego (EOG)**. Dalsze trasowanie i transgraniczne przekazywanie danych do krajĂłw trzecich (w szczegĂłlnoĹ›ci StanĂłw Zjednoczonych Ameryki) legitymizowane jest przez:

- **EU-U.S. Data Privacy Framework (DPF)** â€” certyfikacja strony przyjmujÄ…cej uznana przez KomisjÄ™ EuropejskÄ… decyzjÄ… z 10 lipca 2023 r.; lub
- **Standardowe Klauzule Umowne (SCC)** â€” zatwierdzone przez KomisjÄ™ EuropejskÄ… decyzjÄ… z 4 czerwca 2021 r. (Dz. Urz. UE L 199/31).

---

## Dane kontaktowe Administratora

*[UzupeĹ‚nij danymi Administratora danych â€” peĹ‚na nazwa, adres, e-mail DPO/IOD]*

**Administrator danych osobowych:** [Nazwa firmy/osoby]  
**Adres:** [Adres korespondencyjny]  
**E-mail do kontaktu w sprawach prywatnoĹ›ci:** [privacy@example.com]  
**Inspektor Ochrony Danych (IOD/DPO):** [ImiÄ™ Nazwisko / e-mail, jeĹ›li dotyczy]

---

*Dokument ostatnio aktualizowany: 2026-02-26*  
*Wersja: 1.0*  
*ZgodnoĹ›Ä‡: RODO art. 13/14 Â· PKE 2024 art. 399-400 Â· GCMv2*