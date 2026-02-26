# 🧪 Bootstrap Module - Testing Instructions

## Быстрая проверка работы модуля

### 1️⃣ Активируйте плагин

После активации плагин автоматически инициализирует Bootstrap модуль.

### 2️⃣ Настройте GTM (опционально)

Если у вас есть GTM контейнер:

```php
// Добавьте в functions.php темы
add_action('init', function() {
    update_option('ru_consent_mode_settings', [
        'inject_gtm_loader' => true,
        'gtm_container_id'  => 'GTM-XXXXXXX', // Замените на свой ID
    ]);
}, 5);
```

### 3️⃣ Откройте сайт

Зайдите на любую страницу вашего WordPress сайта.

### 4️⃣ Откройте консоль браузера

Нажмите `F12` (или `Ctrl+Shift+I` на Windows, `Cmd+Option+I` на Mac)

### 5️⃣ Проверьте наличие элементов

#### Проверка dataLayer
```javascript
console.log(window.dataLayer);
// Должен вывести массив с событиями
```

Ожидаемый результат:
```javascript
[
  ['consent', 'default', {
    ad_storage: 'denied',
    ad_user_data: 'denied',
    ad_personalization: 'denied',
    analytics_storage: 'denied',
    functionality_storage: 'granted',
    personalization_storage: 'denied',
    security_storage: 'granted'
  }],
  ['set', 'ads_data_redaction', true]
  // ... другие события
]
```

#### Проверка gtag()
```javascript
console.log(typeof window.gtag);
// Должен вывести: "function"
```

#### Проверка GTM (если настроен)
```javascript
console.log(window.google_tag_manager);
// Должен вывести объект с вашим контейнером
```

### 6️⃣ Тестирование consent update

```javascript
// Принять все согласия
gtag('consent', 'update', {
    ad_storage: 'granted',
    analytics_storage: 'granted',
    ad_user_data: 'granted',
    ad_personalization: 'granted'
});

// Проверить dataLayer
console.log(window.dataLayer);
// Последний элемент должен содержать consent update
```

### 7️⃣ Проверка исходного кода

Откройте исходный код страницы (Ctrl+U) и найдите:

#### Скрипт инициализации (в <head>)
```html
<!-- RU Consent Mode (GCMv2) - Default Consent State -->
<script data-consent-mode="default">
(function() {
    'use strict';
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    ...
})();
</script>
```

#### GTM loader (если настроен, в <head>)
```html
<!-- Google Tag Manager (RU Consent Mode) -->
<script data-gtm-id="GTM-XXXXXXX">
(function(w,d,s,l,i){
    ...
})(window,document,'script','dataLayer','GTM-XXXXXXX');
</script>
```

#### GTM noscript (после <body>)
```html
<!-- Google Tag Manager (noscript) -->
<noscript>
    <iframe src="https://www.googletagmanager.com/ns.html?id=GTM-XXXXXXX" ...>
    </iframe>
</noscript>
```

## ✅ Контрольный список

- [ ] Плагин активирован
- [ ] GTM настроен (если требуется)
- [ ] `window.dataLayer` существует
- [ ] `window.gtag` является функцией
- [ ] Default consent установлен
- [ ] GTM контейнер загружен (если настроен)
- [ ] Нет ошибок в консоли
- [ ] Нет дублирования dataLayer
- [ ] Consent update работает

## 🔍 Продвинутое тестирование

### Использование тестовой страницы

1. Откройте `tests/manual/test-page.html` в браузере
2. Все тесты запустятся автоматически
3. Проверьте результаты каждого теста

### GTM Preview Mode

1. Откройте GTM → Preview
2. Введите URL вашего сайта
3. Проверьте события:
   - Consent Initialization
   - Consent Default
   - Page View
4. В переменных проверьте consent state

### Network Tab

1. Откройте DevTools → Network
2. Обновите страницу
3. Найдите запрос к `gtm.js`
4. Проверьте параметры запроса

## 🐛 Что проверить если не работает

### dataLayer не создается

**Проверьте:**
1. Плагин активирован?
2. Нет ошибок PHP? (проверьте `wp-content/debug.log`)
3. Тема использует `wp_head()`?
4. Нет конфликтов с другими плагинами?

**Решение:**
```php
// Проверьте хуки
add_action('wp_head', function() {
    echo '<!-- wp_head fired -->';
}, 999);
```

### GTM не загружается

**Проверьте:**
1. `inject_gtm_loader` = true?
2. GTM Container ID корректный?
3. Формат: `GTM-XXXXXXX`
4. Нет блокировки ad-blocker?

**Решение:**
```php
// Проверьте настройки
$settings = get_option('ru_consent_mode_settings');
var_dump($settings);
```

### Ошибка: "gtag is not defined"

**Причина:** Bootstrap не инициализировался

**Решение:**
1. Проверьте приоритет хука (должен быть 0)
2. Убедитесь что нет конфликтов
3. Очистите кеш

### Дублирование dataLayer

**Причина:** Другой скрипт уже создал dataLayer

**Решение:**
Bootstrap автоматически проверяет:
```javascript
window.dataLayer = window.dataLayer || [];
```

Это безопасно и не создаст дубликатов.

## 📊 Ожидаемые результаты

### Минимальная конфигурация (без GTM)

```javascript
// Должно быть в window.dataLayer:
[
  ['consent', 'default', {...}],
  ['set', 'ads_data_redaction', true]
]
```

### Полная конфигурация (с GTM)

```javascript
// Должно быть в window.dataLayer:
[
  ['consent', 'default', {...}],
  ['set', 'ads_data_redaction', true],
  {'gtm.start': ..., event: 'gtm.js'},
  // GTM события...
]

// Должно существовать:
window.google_tag_manager['GTM-XXXXXXX']
```

## 🎯 Следующие шаги

После успешного тестирования Bootstrap модуля:

1. ✅ Bootstrap работает
2. 🔄 Настройте GTM теги с учетом consent
3. 🔄 Реализуйте Frontend баннер
4. 🔄 Добавьте Admin панель
5. 🔄 Настройте логирование

## 📞 Помощь

Если что-то не работает:

1. Проверьте [Quick Start Guide](../docs/QUICK_START.md)
2. Изучите [Technical Documentation](../TECHNICAL.md)
3. Используйте [Test Page](test-page.html)
4. Создайте issue на GitHub

---

**Версия:** 1.0.0  
**Дата:** 23 октября 2025  
**Модуль:** Bootstrap (src/Consent/Bootstrap.php)
