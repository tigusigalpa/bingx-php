# Changelog - Wallet & Account API Implementation

## Новые возможности

### 🆕 WalletService
Новый сервис для управления кошельком и операциями с депозитами/выводами.

**Методы:**
- `getDepositHistory()` - История депозитов с фильтрацией
- `getWithdrawalHistory()` - История выводов средств
- `getAllCoinInfo()` - Информация о всех доступных монетах
- `getDepositAddress()` - Получение адреса для депозита
- `getDepositRiskRecords()` - Записи контроля рисков депозитов
- `withdraw()` - Создание запроса на вывод средств

**API Endpoints:**
- `GET /openApi/api/v3/capital/deposit/hisrec`
- `GET /openApi/api/v3/capital/withdraw/history`
- `GET /openApi/wallets/v1/capital/config/getall`
- `GET /openApi/wallets/v1/capital/deposit/address`
- `GET /openApi/wallets/v1/capital/deposit/riskRecords`
- `POST /openApi/wallets/v1/capital/withdraw/apply`

### 🆕 SpotAccountService
Новый сервис для работы со спотовым аккаунтом и трансферами.

**Методы:**
- `getBalance()` - Баланс спотового аккаунта
- `getAssetTransferRecords()` - История трансферов между аккаунтами
- `universalTransfer()` - Универсальный трансфер между аккаунтами
- `internalTransfer()` - Внутренний перевод между основным и суб-аккаунтами
- `getSupportedTransferCoins()` - Список поддерживаемых монет для трансфера
- `getInternalTransferRecords()` - История внутренних переводов
- `getFundBalance()` - Баланс фонда
- `getMainAccountInternalTransferRecords()` - История внутренних переводов основного аккаунта
- `getAllAccountBalances()` - Балансы всех аккаунтов

**API Endpoints:**
- `GET /openApi/spot/v1/account/balance`
- `GET /openApi/api/v3/asset/transfer`
- `POST /openApi/api/asset/v1/transfer`
- `POST /openApi/wallets/v1/capital/innerTransfer/apply`
- `GET /openApi/api/asset/v1/transfer/supportCoins`
- `GET /openApi/api/v3/asset/transferRecord`
- `GET /openApi/fund/v1/account/balance`
- `GET /openApi/wallets/v1/capital/innerTransfer/records`
- `GET /openApi/account/v1/allAccountBalance`

## Изменения в существующих файлах

### BingxClient.php
**Добавлено:**
- Импорт `WalletService` и `SpotAccountService`
- Свойства `$wallet` и `$spotAccount`
- Инициализация сервисов в конструкторе
- Методы `wallet()` и `spotAccount()` для доступа к сервисам

### README.md
**Добавлено:**
- Описание новых сервисов в разделе "Модульная архитектура"
- Полная документация по WalletService с примерами
- Полная документация по SpotAccountService с примерами
- Примеры использования в чистом PHP
- Обновленная статистика: 119+ методов (было 99+)
- Новые возможности в списке ключевых функций

## Новые файлы

### WalletService.php
Полная реализация Wallet API с 6 методами для управления депозитами, выводами и адресами кошельков.

### SpotAccountService.php
Полная реализация Spot Account API с 9 методами для управления балансом, трансферами и внутренними переводами.

### WALLET_API_EXAMPLES.md
Подробные примеры использования новых API с практическими сценариями:
- Работа с депозитами и выводами
- Управление балансами
- Универсальные и внутренние трансферы
- Комплексный пример автоматизации
- Обработка ошибок
- Мониторинг и отчетность

## Использование

### Laravel (через фасад)

```php
use Tigusigalpa\BingX\Facades\Bingx;

// Wallet API
$deposits = Bingx::wallet()->getDepositHistory('USDT');
$address = Bingx::wallet()->getDepositAddress('USDT', 'TRC20');
$withdrawal = Bingx::wallet()->withdraw('USDT', 'TXxx...xxx', 100.0, 'TRC20');

// Spot Account API
$balance = Bingx::spotAccount()->getBalance();
$transfer = Bingx::spotAccount()->universalTransfer('FUND_PFUTURES', 'USDT', 100);
$internalTransfer = Bingx::spotAccount()->internalTransfer(
    'USDT', 'SPOT', 50.0, 'FROM_MAIN_TO_SUB', '123456'
);
```

### Чистый PHP

```php
$bingx = new \Tigusigalpa\BingX\BingxClient($apiKey, $apiSecret);

// Wallet API
$deposits = $bingx->wallet()->getDepositHistory('USDT');
$address = $bingx->wallet()->getDepositAddress('USDT', 'TRC20');

// Spot Account API
$balance = $bingx->spotAccount()->getBalance();
$transfer = $bingx->spotAccount()->universalTransfer('FUND_PFUTURES', 'USDT', 100);
```

## Совместимость

- ✅ Laravel 8.x, 9.x, 10.x, 11.x, 12.x
- ✅ PHP 8.0+
- ✅ Полная обратная совместимость с существующим кодом
- ✅ Все существующие методы работают без изменений

## Тестирование

Рекомендуется протестировать новые методы в тестовой среде перед использованием в продакшене:

```php
// Тестирование получения адреса депозита
$address = Bingx::wallet()->getDepositAddress('USDT', 'TRC20');
var_dump($address);

// Тестирование баланса
$balance = Bingx::spotAccount()->getBalance();
var_dump($balance);
```

## Безопасность

⚠️ **Важно:**
- Методы вывода средств требуют дополнительных прав API
- Убедитесь, что ваш API ключ имеет необходимые разрешения
- Используйте IP whitelist для дополнительной безопасности
- Храните API ключи в безопасном месте (`.env` файл)

## Документация BingX API

Официальная документация:
- [Wallet API](https://bingx-api.github.io/docs/#/en-us/common/wallet-api.html)
- [Account API](https://bingx-api.github.io/docs/#/en-us/common/account-api.html)
- [Spot Account API](https://bingx-api.github.io/docs/#/en-us/spot/account-api.html)

## Поддержка

При возникновении проблем или вопросов:
1. Проверьте документацию в README.md
2. Изучите примеры в WALLET_API_EXAMPLES.md
3. Убедитесь, что API ключ имеет необходимые права
4. Проверьте официальную документацию BingX API
