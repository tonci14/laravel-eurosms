
# Laravel EuroSMS 📱

Laravel balíček na odosielanie SMS správ cez [EuroSMS](https://www.eurosms.com) API.

Podporuje:
- Synchronné aj asynchrónne odosielanie správ
- Logovanie odoslaných SMS do databázy
- Validáciu čísel vo formáte E.164 (s povolenými krajinami)
- Laravel queue (joby)
- Facade `EuroSms::send(...)` a `EuroSms::sendAsync(...)`

---
## 💡 Inštalácia

```bash
composer require tonci14/laravel-eurosms
```

Publikuj config a migrácie:

```bash
php artisan vendor:publish --provider="Tonci14\LaravelEuroSMS\LaravelEuroSMSServiceProvider" --tag=eurosms-config
php artisan migrate
```

---

## ⚙️ Konfigurácia

V `.env` súbore:

```env
EURO_SMS_URL=https://as.eurosms.com/sms/Sender
EUROSMS_KEY= -- provided key --
EUROSMS_ID= -- privided ID --
SMS_SENDER_NAME= -- use custom name up to 10 letters --
```

V `config/eurosms.php`:

```php
return [
    'url'            => env('EURO_SMS_URL', 'https://as.eurosms.com/sms/Sender'),
    'integrationKey' => env('EUROSMS_KEY', null),
    'integrationID'  => env('EUROSMS_ID', null),
    'senderName'     => env('SMS_SENDER_NAME', null),

    // Povolené krajiny podľa kódu ISO (2-písmenový) napr: 'SK', 'CZ', 'AT'
    'allowed_countries' => [],
];
```

### 🔹 Inštalácia

```bash
php artisan vendor:publish --tag=eurosms-migrations
php artisan vendor:publish --tag=eurosms-config

php artisan migrate
```

---

## 🚀 Použitie

### 🔹 Odoslanie správy synchronne

```php
use EuroSms;

EuroSms::send('+421900123456', 'Ahoj, toto je testovacia správa.');
```

### 🔹 Odoslanie správy asynchrónne (cez queue)

```php
EuroSms::sendAsync(
    '+421900123456',
    'Toto ide cez queue',
    locale: null,
    queue: 'messaging',
    userId: auth()->id()
);
```

> Asynchrónna správa sa vloží do queue a uloží do tabuľky `euro_sms_queue`.

---

## ✅ Validácia čísel

Pred každým odoslaním je číslo:

- Validované pomocou knižnice `giggsey/libphonenumber-for-php`
- Uložené vo formáte E.164 (napr. `+421900123456`)
- Skontrolované, či je z povolenej krajiny (napr. `SK`, `CZ`, `AT`)

### ❌ Chybové situácie

```php
EuroSms::send('0900123456', 'Test');
// ➜ Invalid phone number format

EuroSms::send('+441234567890', 'UK test');
// ➜ Phone number region 'GB' is not allowed
```

---

## 🗂️ Databáza: `euro_sms_queue`

Každá správa sa loguje do databázy:

| Stĺpec                     | Popis                       |
|----------------------------|-----------------------------|
| `id`                       | Primárny kľúč               |
| `user_id`                  | Voliteľný ID používateľa    |
| `phone`                    | Telefónne číslo             |
| `message`                  | Obsah správy                |
| `status`                   | `sent`, `failed`, `pending` |
| `error`                    | Chyba (ak nastala)          |
| `sent_at`                  | Čas odoslania správy        |
| `created_at`, `updated_at` | Laravel timestamps          |

---

## 🧪 Testovanie

Spusť testy:

```bash
php artisan test
```

Testuje sa:
- synchronné/asynchrónne odoslanie
- logovanie do databázy
- validácia čísel
- chybové stavy

---

## 📚 Roadmap

- [ ] Šablóny správ (viacjazyčné)
- [ ] Retry logika pre `failed` správy
- [ ] Webhook listener na spätné stavy
- [ ] Laravel Notification support

---

## 👤 Autor

Vytvoril: [Tonci14](https://github.com/tonci14)  

---

## 🪪 Licencia

MIT
