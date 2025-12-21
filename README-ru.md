# BingX PHP SDK

![BingX PHP SDK](https://github.com/user-attachments/assets/bc9acf4c-79c7-4e02-bb8d-75f2d8784b29)
<div align="center">

[![PHP Version](https://img.shields.io/badge/php-%3E%3D8.1-blue?style=flat-square&logo=php)](https://www.php.net/)
[![Composer](https://img.shields.io/badge/composer-v2-orange?style=flat-square&logo=composer)](https://getcomposer.org/)
[![License](https://img.shields.io/badge/license-MIT-green?style=flat-square)](LICENSE)
[![GitHub Stars](https://img.shields.io/github/stars/tigusigalpa/bingx-php?style=flat-square&logo=github)](https://github.com/tigusigalpa/bingx-php)
[![Latest Release](https://img.shields.io/github/v/release/tigusigalpa/bingx-php?style=flat-square&logo=github)](https://github.com/tigusigalpa/bingx-php/releases)
[![Test Coverage](https://img.shields.io/badge/coverage-119%2B%20tests-brightgreen?style=flat-square)](#-тестирование)

**Полнофункциональный PHP SDK для BingX API**

USDT-M & Coin-M Фьючерсы | Рыночные данные | WebSocket потоки | Интеграция с Laravel

**🌐 Язык:** Русский | [English](README.md)
</div>

---

## 📖 Содержание

- [О библиотеке](#-о-библиотеке)
- [Возможности](#-возможности)
- [Быстрый старт](#-быстрый-старт)
- [Установка](#-установка)
- [Использование](#-использование)
    - [Market Service](#-market-service---рыночные-данные)
    - [Quote API](#-quote-api---оптимизированные-рыночные-данные)
    - [TWAP Orders](#-twap-orders---алгоритмическая-торговля)
    - [Account Service](#-account-service---управление-аккаунтом)
    - [Trade Service](#-trade-service---торговые-операции)
    - [Wallet Service](#-wallet-service---управление-кошельком)
    - [Spot Account Service](#-spot-account-service---спотовый-аккаунт)
    - [Contract Service](#-contract-service---стандартные-фьючерсы)
    - [WebSocket API](#-websocket-api)
    - [Coin-M Perpetual Futures](#-coin-m-perpetual-futures---контракты-с-крипто-маржой)
- [OrderBuilder](#-orderbuilder---расширенное-создание-ордеров)
- [Обработка ошибок](#-обработка-ошибок)
- [Тестирование](#-тестирование)
- [Документация](#-документация)
- [Версии](#-версии)
- [Лицензия](#-лицензия)

---

## ✨ О библиотеке

**BingX PHP SDK** — профессиональная, многофункциональная библиотека для работы с BingX API (USDT-M и Coin-M
бессрочные фьючерсы).

Создана с использованием современных практик PHP и предоставляет:

- ✅ **100% покрытие** USDT-M Perpetual Futures API
- ✅ **Coin-M Perpetual Futures** полностью реализован
- ✅ **Модульная архитектура** с отдельными сервисами
- ✅ **Интеграция с Laravel 8-12** с автоопределением
- ✅ **Расширенная обработка ошибок** с пользовательскими исключениями
- ✅ **Поддержка WebSocket** для потоковых данных
- ✅ **Полная безопасность** с подписями HMAC-SHA256
- ✅ **220 методов** для полного контроля торговли
- ✅ **Quote API** для оптимизированных рыночных данных
- ✅ **TWAP ордера** для алгоритмической торговли

---

## 🚀 Возможности

### 📊 Поддерживаемые сервисы

| Сервис                       | Описание                                                         | Методов |
|------------------------------|------------------------------------------------------------------|---------|
| **USDT-M Perpetual Futures** |                                                                  |         |
| 🏪 **Market Service**        | Рыночные данные, Quote API, символы, цены, свечи                 | 40      |
| ⏱️ **TWAP Service**          | Алгоритмические ордера с временным взвешиванием                  | 7       |
| 👤 **Account Service**       | Баланс, позиции, кредитное плечо, маржа, активы                  | 39      |
| 🔄 **Trade Service**         | Ордера, история сделок, управление позициями                     | 54      |
| 💰 **Wallet Service**        | Депозиты, выводы, адреса кошельков                               | 6       |
| 💵 **Spot Account Service**  | Спотовый баланс, переводы, внутренние переводы                   | 8       |
| 👥 **Sub-Account Service**   | Управление субаккаунтами, API ключи, переводы                    | 20      |
| 📊 **Copy Trading Service**  | Копи-трейдинг для фьючерсов и спота                              | 13      |
| 📋 **Contract Service**      | Стандартный API контрактов                                       | 3       |
| 🔐 **Listen Key Service**    | Аутентификация WebSocket                                         | 3       |
| **Coin-M Perpetual Futures** |                                                                  |         |
| 🪙 **Coin-M Market**         | Информация о контрактах, тикер, глубина, свечи, открытый интерес | 6       |
| 🪙 **Coin-M Trade**          | Ордера, позиции, кредитное плечо, маржа, баланс                  | 17      |
| 🪙 **Coin-M Listen Key**     | Аутентификация WebSocket для Coin-M                              | 3       |

### 🛡️ Безопасность

- ✅ Подпись HMAC-SHA256 для всех запросов
- ✅ Автоматическая валидация временных меток
- ✅ Поддержка кодирования подписи base64 и hex
- ✅ recvWindow для защиты от повторных атак
- ✅ Пользовательские исключения для разных типов ошибок

### 🔧 Удобство разработки

- ✅ Fluent интерфейс для создания ордеров
- ✅ Автодополнение IDE с подсказками типов
- ✅ Подробные сообщения об ошибках
- ✅ Полное покрытие тестами с примерами
- ✅ Поддержка чистого PHP и Laravel

---

## ⚡ Быстрый старт

### С Laravel

```php
// Получить текущую цену
$price = Bingx::market()->getLatestPrice('BTC-USDT');
echo "Цена BTC: {$price['price']}";

// Получить баланс аккаунта
$balance = Bingx::account()->getBalance();

// Создать ордер используя OrderBuilder
$order = Bingx::trade()->order()
    ->futures()
    ->symbol('BTC-USDT')
    ->buy()
    ->long()
    ->type('LIMIT')
    ->margin(100)
    ->price(50000)
    ->leverage(10)
    ->stopLossPercent(5)
    ->takeProfitPercent(15)
    ->execute();
```

### Чистый PHP

```php
use Tigusigalpa\BingX\BingxClient;
use Tigusigalpa\BingX\Http\BaseHttpClient;

$http = new BaseHttpClient('API_KEY', 'API_SECRET', 'https://open-api.bingx.com');
$bingx = new BingxClient($http);

$price = $bingx->market()->getLatestPrice('BTC-USDT');
```

---

## 📦 Установка

### Требования

- PHP >= 8.1
- Composer
- (Опционально) Laravel 8-12 для интеграции

### Шаг 1: Добавить репозиторий

В корневой `composer.json`:

```json
{
    "repositories": [
        {
            "type": "path",
            "url": "packages/bingx-php"
        }
    ]
}
```

### Шаг 2: Установить пакет

```bash
composer require tigusigalpa/bingx-php:*
```

### Шаг 3: (Только Laravel) Опубликовать конфигурацию

```bash
php artisan vendor:publish --tag=bingx-config
```

### Шаг 4: Настроить переменные окружения

Добавить в `.env`:

```env
BINGX_API_KEY=your_api_key_here
BINGX_API_SECRET=your_api_secret_here
BINGX_SOURCE_KEY=optional_source_key
BINGX_BASE_URI=https://open-api.bingx.com
BINGX_SIGNATURE_ENCODING=base64
```

### 🔑 Создание API ключей

1. Перейдите в [BingX API Settings](https://bingx.com/en-US/accounts/api)
2. Нажмите "Create API"
3. Сохраните **API Key** и **Secret Key** в безопасном месте
4. Настройте права доступа
5. ⚠️ Secret Key отображается только один раз!

---

## 📚 Использование

### 🏪 Market Service - Рыночные данные

#### Торговые пары и символы

```php
// Получить все доступные символы (спот + фьючерсы)
$allSymbols = Bingx::market()->getAllSymbols();
// ['spot' => [...], 'futures' => [...]]

// Только спотовые пары
$spotSymbols = Bingx::market()->getSpotSymbols();

// Только фьючерсные символы
$futuresSymbols = Bingx::market()->getFuturesSymbols();
```

#### Цены и статистика

```php
// Текущая цена
$futuresPrice = Bingx::market()->getLatestPrice('BTC-USDT');
$spotPrice = Bingx::market()->getSpotLatestPrice('BTC-USDT');

// 24-часовая статистика
$ticker = Bingx::market()->get24hrTicker('BTC-USDT');
$spotTicker = Bingx::market()->getSpot24hrTicker('BTC-USDT');

// Все символы сразу
$allTickers = Bingx::market()->get24hrTicker();
```

#### Глубина рынка и свечи

```php
// Глубина стакана ордеров
$depth = Bingx::market()->getDepth('BTC-USDT', 20);
$spotDepth = Bingx::market()->getSpotDepth('BTC-USDT', 20);

// Свечи
$klines = Bingx::market()->getKlines('BTC-USDT', '1h', 100);
$spotKlines = Bingx::market()->getSpotKlines('BTC-USDT', '1h', 100);

// С временным диапазоном
$klines = Bingx::market()->getKlines(
    'BTC-USDT', '1h', 100,
    strtotime('2024-01-01') * 1000,
    strtotime('2024-01-02') * 1000
);
```

#### Ставка финансирования и маркированная цена

```php
// История ставки финансирования
$fundingRate = Bingx::market()->getFundingRateHistory('BTC-USDT', 100);

// Маркированная цена
$markPrice = Bingx::market()->getMarkPrice('BTC-USDT');

// Индекс премии
$premiumKlines = Bingx::market()->getPremiumIndexKlines('BTC-USDT', '1h', 100);

// Непрерывные контракты
$continuousKlines = Bingx::market()->getContinuousKlines('BTC-USDT', '1h', 100);
```

#### Сделки

```php
// Агрегированные сделки
$aggTrades = Bingx::market()->getAggregateTrades('BTC-USDT', 500);

// Недавние сделки
$recentTrades = Bingx::market()->getRecentTrades('BTC-USDT', 500);

// Спотовые сделки
$spotAggTrades = Bingx::market()->getSpotAggregateTrades('BTC-USDT', 500);
$spotRecentTrades = Bingx::market()->getSpotRecentTrades('BTC-USDT', 500);
```

---

### 📊 Quote API - Оптимизированные рыночные данные

Quote API предоставляет рыночные данные в реальном времени с улучшенной производительностью для высокочастотной
торговли.

```php
// Получить все спецификации контрактов
$contracts = Bingx::market()->getContracts();

// Стакан ордеров через Quote API (оптимизированный)
$depth = Bingx::market()->getQuoteDepth('BTC-USDT', 20);

// 24-часовой тикер через Quote API
$ticker = Bingx::market()->getQuoteTicker('BTC-USDT');

// Ставка финансирования
$fundingRate = Bingx::market()->getQuoteFundingRate('BTC-USDT');

// Открытый интерес
$openInterest = Bingx::market()->getQuoteOpenInterest('BTC-USDT');
```

---

### ⏱️ TWAP Orders - Алгоритмическая торговля

TWAP (Time-Weighted Average Price) ордера позволяют исполнять крупные ордера со временем для минимизации влияния на
рынок.

```php
// Простой TWAP ордер на покупку
$order = Bingx::twap()->buy(
    symbol: 'BTC-USDT',
    quantity: 1.0,
    duration: 1800, // 30 минут в секундах
    price: null, // null = рыночная цена
    positionSide: 'LONG'
);

// Отменить TWAP ордер
Bingx::twap()->cancelOrder('orderId', 'BTC-USDT');

// Получить открытые TWAP ордера
$openOrders = Bingx::twap()->getOpenOrders('BTC-USDT');
```

---

### 👤 Account Service - Управление аккаунтом

```php
// Получить баланс аккаунта
$balance = Bingx::account()->getBalance();

// Все позиции
$allPositions = Bingx::account()->getPositions();

// Получить текущее кредитное плечо
$leverage = Bingx::account()->getLeverage('BTC-USDT');

// Установить кредитное плечо
Bingx::account()->setLeverage('BTC-USDT', 'BOTH', 20);

// Режим маржи (ISOLATED или CROSSED)
$marginMode = Bingx::account()->getMarginMode('BTC-USDT');
Bingx::account()->setMarginMode('BTC-USDT', 'ISOLATED');
```

---

### 🔄 Trade Service - Торговые операции

```php
// Спотовые ордера
$buy = Bingx::trade()->spotMarketBuy('BTC-USDT', 0.001);
$sell = Bingx::trade()->spotMarketSell('BTC-USDT', 0.001);

// Фьючерсные ордера
$longOrder = Bingx::trade()->futuresLongMarket('BTC-USDT', 100, 10);
$shortOrder = Bingx::trade()->futuresShortMarket('BTC-USDT', 100, 10);

// Создать ордер
$order = Bingx::trade()->createOrder([
    'symbol' => 'BTC-USDT',
    'side' => 'BUY',
    'type' => 'MARKET',
    'quantity' => 0.001
]);

// Отменить конкретный ордер
Bingx::trade()->cancelOrder('BTC-USDT', '123456789');

// Отменить все ордера для символа
Bingx::trade()->cancelAllOrders('BTC-USDT');
```

---

### 💰 Wallet Service - Управление кошельком

```php
// История депозитов
$deposits = Bingx::wallet()->getDepositHistory(
    coin: 'USDT',
    status: 1,
    startTime: strtotime('2024-01-01') * 1000,
    endTime: strtotime('2024-01-31') * 1000
);

// Адрес для депозита
$address = Bingx::wallet()->getDepositAddress('USDT', 'TRC20');

// История выводов
$withdrawals = Bingx::wallet()->getWithdrawalHistory(
    coin: 'USDT',
    status: 6
);

// Создать вывод
$withdrawal = Bingx::wallet()->withdraw(
    coin: 'USDT',
    address: 'TXxx...xxx',
    amount: 100.0,
    network: 'TRC20'
);

// Информация о монетах
$coins = Bingx::wallet()->getAllCoinInfo();
```

---

### 💵 Spot Account Service - Спотовый аккаунт

```php
// Баланс спотового аккаунта
$balance = Bingx::spotAccount()->getBalance();

// Баланс фонда
$fundBalance = Bingx::spotAccount()->getFundBalance();

// Универсальный перевод
$transfer = Bingx::spotAccount()->universalTransfer(
    type: 'FUND_PFUTURES',
    asset: 'USDT',
    amount: 100.0
);

// История переводов
$history = Bingx::spotAccount()->getAssetTransferRecords(
    type: 'FUND_PFUTURES',
    startTime: strtotime('2024-01-01') * 1000,
    endTime: strtotime('2024-01-31') * 1000
);

// Внутренний перевод (основной -> суб-аккаунт)
$internalTransfer = Bingx::spotAccount()->internalTransfer(
    coin: 'USDT',
    walletType: 'SPOT',
    amount: 50.0,
    transferType: 'FROM_MAIN_TO_SUB',
    subUid: '123456'
);

// Получить балансы всех аккаунтов
$allBalances = Bingx::spotAccount()->getAllAccountBalances();
```

---

### 👥 Sub-Account Service - Управление субаккаунтами

Полное управление субаккаунтами включая создание, управление API ключами, переводы и мониторинг.

#### Создание и управление субаккаунтами

```php
// Создать новый субаккаунт
$result = Bingx::subAccount()->createSubAccount('sub_account_001');

// Получить UID аккаунта
$uid = Bingx::subAccount()->getAccountUid();

// Получить список всех субаккаунтов
$subAccounts = Bingx::subAccount()->getSubAccountList();

// Получить конкретный субаккаунт с пагинацией
$subAccounts = Bingx::subAccount()->getSubAccountList(
    subAccountString: 'sub_account_001',
    current: 1,
    size: 10
);

// Получить активы субаккаунта
$assets = Bingx::subAccount()->getSubAccountAssets('12345678');

// Обновить статус субаккаунта
Bingx::subAccount()->updateSubAccountStatus('sub_account_001', 1); // 1: включить, 2: отключить

// Получить балансы всех субаккаунтов
$balances = Bingx::subAccount()->getAllSubAccountBalances();
```

#### Управление API ключами субаккаунтов

```php
// Создать API ключ для субаккаунта
$apiKey = Bingx::subAccount()->createSubAccountApiKey(
    subAccountString: 'sub_account_001',
    label: 'Trading Bot',
    permissions: ['spot' => true, 'futures' => true],
    ip: '192.168.1.1' // Опциональный IP whitelist
);

// Запросить информацию об API ключе
$apiKeys = Bingx::subAccount()->queryApiKey('sub_account_001');

// Редактировать API ключ субаккаунта
Bingx::subAccount()->editSubAccountApiKey(
    subAccountString: 'sub_account_001',
    apiKey: 'your_api_key',
    permissions: ['spot' => true, 'futures' => false],
    ip: '192.168.1.100'
);

// Удалить API ключ субаккаунта
Bingx::subAccount()->deleteSubAccountApiKey('sub_account_001', 'your_api_key');
```

#### Переводы субаккаунтов

```php
// Авторизовать субаккаунт для внутренних переводов
Bingx::subAccount()->authorizeSubAccountInternalTransfer('sub_account_001', 1); // 1: разрешить, 0: запретить

// Перевод с основного на субаккаунт
$transfer = Bingx::subAccount()->subAccountInternalTransfer(
    coin: 'USDT',
    walletType: 'SPOT',
    amount: 100.0,
    transferType: 'FROM_MAIN_TO_SUB',
    toSubUid: '12345678'
);

// Перевод с субаккаунта на основной
$transfer = Bingx::subAccount()->subAccountInternalTransfer(
    coin: 'USDT',
    walletType: 'SPOT',
    amount: 50.0,
    transferType: 'FROM_SUB_TO_MAIN',
    fromSubUid: '12345678'
);

// Перевод между субаккаунтами
$transfer = Bingx::subAccount()->subAccountInternalTransfer(
    coin: 'USDT',
    walletType: 'PERPETUAL',
    amount: 25.0,
    transferType: 'FROM_SUB_TO_SUB',
    fromSubUid: '12345678',
    toSubUid: '87654321',
    clientId: 'transfer-001'
);

// Получить записи внутренних переводов
$records = Bingx::subAccount()->getSubAccountInternalTransferRecords(
    startTime: strtotime('-7 days') * 1000,
    endTime: time() * 1000,
    current: 1,
    size: 50
);

// Перевод активов субаккаунта
$assetTransfer = Bingx::subAccount()->subAccountAssetTransfer(
    subUid: '12345678',
    type: 'FUND_PFUTURES',
    asset: 'USDT',
    amount: 100.0
);

// Получить поддерживаемые монеты для переводов субаккаунта
$supportedCoins = Bingx::subAccount()->getSubAccountTransferSupportedCoins('12345678');

// Получить историю переводов активов
$history = Bingx::subAccount()->getSubAccountAssetTransferHistory(
    subUid: '12345678',
    type: 'FUND_PFUTURES',
    startTime: strtotime('-30 days') * 1000,
    endTime: time() * 1000
);
```

#### Управление депозитами субаккаунтов

```php
// Создать депозитный адрес для субаккаунта
$address = Bingx::subAccount()->createSubAccountDepositAddress(
    coin: 'USDT',
    network: 'TRC20',
    subUid: '12345678'
);

// Получить депозитный адрес субаккаунта
$depositAddress = Bingx::subAccount()->getSubAccountDepositAddress(
    coin: 'USDT',
    subUid: '12345678',
    network: 'TRC20'
);

// Получить историю депозитов субаккаунта
$deposits = Bingx::subAccount()->getSubAccountDepositHistory(
    subUid: '12345678',
    coin: 'USDT',
    status: 1, // 0: в ожидании, 1: успешно, 6: зачислено но нельзя вывести
    startTime: strtotime('-30 days') * 1000,
    endTime: time() * 1000,
    limit: 100
);
```

---

### 📊 Copy Trading Service - Копи-трейдинг

Полная функциональность копи-трейдинга для бессрочных фьючерсов и спотовой торговли.

#### Копи-трейдинг фьючерсов

```php
// Получить текущие ордера отслеживания
$orders = Bingx::copyTrading()->getCurrentTrackOrders('BTC-USDT');

// Закрыть позицию по номеру ордера
$result = Bingx::copyTrading()->closeTrackOrder('1252864099381234567');

// Установить тейк-профит и стоп-лосс
Bingx::copyTrading()->setTPSL(
    positionId: '1252864099381234567',
    stopLoss: 48000.0,
    takeProfit: 52000.0
);

// Получить детали трейдера
$details = Bingx::copyTrading()->getTraderDetail();

// Получить сводку прибыли
$summary = Bingx::copyTrading()->getProfitSummary();

// Получить детали прибыли с пагинацией
$profits = Bingx::copyTrading()->getProfitDetail(
    pageIndex: 1,
    pageSize: 20
);

// Установить комиссию
Bingx::copyTrading()->setCommission(5.0); // 5% комиссия

// Получить доступные торговые пары
$pairs = Bingx::copyTrading()->getTradingPairs();
```

#### Копи-трейдинг спота

```php
// Продать спотовый ордер на основе ID ордера покупки
$result = Bingx::copyTrading()->sellSpotOrder('1253517936071234567');

// Получить детали спотового трейдера
$details = Bingx::copyTrading()->getSpotTraderDetail();

// Получить сводку прибыли спота
$summary = Bingx::copyTrading()->getSpotProfitSummary();

// Получить детали прибыли спота
$profits = Bingx::copyTrading()->getSpotProfitDetail(
    pageIndex: 1,
    pageSize: 20
);

// Запросить исторические спотовые ордера
$history = Bingx::copyTrading()->getSpotHistoryOrders(
    pageIndex: 1,
    pageSize: 50
);
```

---

### 📋 Contract Service - Стандартные фьючерсы

API стандартных фьючерсов предоставляет доступ к позициям, ордерам и балансу стандартных контрактов.

```php
// Получить все позиции по стандартным контрактам
$positions = Bingx::contract()->getAllPositions();

// Получить исторические ордера для конкретного символа
$orders = Bingx::contract()->getAllOrders(
    symbol: 'BTC-USDT',
    limit: 100,
    startTime: strtotime('-7 days') * 1000,
    endTime: time() * 1000
);

// Запросить баланс аккаунта стандартных контрактов
$balance = Bingx::contract()->getBalance();

// С пользовательскими параметрами
$positions = Bingx::contract()->getAllPositions(
    timestamp: time() * 1000,
    recvWindow: 5000
);
```

---

### 🌐 WebSocket API

```php
use Tigusigalpa\BingX\WebSocket\MarketDataStream;

$stream = new MarketDataStream();
$stream->connect();

// Подписаться на события
$stream->subscribeTrade('BTC-USDT');
$stream->subscribeKline('BTC-USDT', '1m');
$stream->subscribeDepth('BTC-USDT', 20);

// Обработка сообщений
$stream->onMessage(function ($data) {
    echo "Данные: " . json_encode($data) . PHP_EOL;
});

// Начать прослушивание
$stream->listen();
```

---

### 🪙 Coin-M Perpetual Futures - Контракты с крипто-маржой

Coin-M бессрочные фьючерсы — это контракты с маржой и расчётами в криптовалюте (например, BTC, ETH) вместо USDT.

#### Ключевые отличия от USDT-M фьючерсов

| Особенность          | USDT-M Фьючерсы     | Coin-M Фьючерсы                |
|----------------------|---------------------|--------------------------------|
| **Валюта маржи**     | USDT (стейблкоин)   | Криптовалюта (BTC, ETH и т.д.) |
| **Расчёты**          | USDT                | Базовая криптовалюта           |
| **API путь**         | `/openApi/swap/v2/` | `/openApi/cswap/v1/`           |
| **Формат символа**   | BTC-USDT            | BTC-USD, ETH-USD               |
| **Деноминация цены** | USD значение        | Контракты на монету            |

#### Рыночные данные

```php
// Информация о контрактах
$contracts = Bingx::coinM()->market()->getContracts();

// Текущая цена и ставка финансирования
$ticker = Bingx::coinM()->market()->getTicker('BTC-USD');

// Открытые позиции (открытый интерес)
$openPositions = Bingx::coinM()->market()->getOpenPositions('BTC-USD');

// Данные K-line
$klines = Bingx::coinM()->market()->getKlines('BTC-USD', '1h', 100);

// Глубина стакана ордеров
$depth = Bingx::coinM()->market()->getDepth('BTC-USD', 20);
```

#### Торговые операции

```php
// Разместить ордер
$order = Bingx::coinM()->trade()->createOrder([
    'symbol' => 'BTC-USD',
    'side' => 'BUY',
    'positionSide' => 'LONG',
    'type' => 'LIMIT',
    'quantity' => 100,
    'price' => 50000
]);

// Получить кредитное плечо
$leverage = Bingx::coinM()->trade()->getLeverage('BTC-USD');

// Установить кредитное плечо
Bingx::coinM()->trade()->setLeverage('BTC-USD', 10);

// Получить позиции
$positions = Bingx::coinM()->trade()->getPositions('BTC-USD');

// Получить баланс аккаунта
$balance = Bingx::coinM()->trade()->getBalance();
```

---

## 🎯 OrderBuilder - Расширенное создание ордеров

OrderBuilder предоставляет удобный fluent интерфейс для создания сложных ордеров с автоматическим расчётом.

```php
// Фьючерсный ордер с кредитным плечом
$order = Bingx::trade()->order()
    ->futures()
    ->symbol('BTC-USDT')
    ->buy()
    ->long()
    ->type('LIMIT')
    ->margin(100)
    ->price(50000)
    ->leverage(10)
    ->execute();

// Лонг с процентными стопами
$order = Bingx::trade()->order()
    ->futures()
    ->symbol('BTC-USDT')
    ->buy()
    ->long()
    ->type('LIMIT')
    ->margin(200)
    ->price(50000)
    ->leverage(10)
    ->stopLossPercent(5)      // Stop Loss 5% ниже
    ->takeProfitPercent(15)   // Take Profit 15% выше
    ->execute();
```

---

## ⚠️ Обработка ошибок

Библиотека предоставляет пользовательские исключения для разных типов ошибок:

```php
use Tigusigalpa\BingX\Exceptions\{
    BingxException,
    AuthenticationException,
    RateLimitException,
    InsufficientBalanceException,
    ApiException
};

try {
    $balance = Bingx::account()->getBalance();
} catch (AuthenticationException $e) {
    // Ошибка аутентификации (неверный ключ/подпись)
    echo "Ошибка авторизации: " . $e->getMessage();
} catch (RateLimitException $e) {
    // Превышен лимит запросов
    echo "Превышен лимит. Повторить после: " . $e->getRetryAfter();
} catch (InsufficientBalanceException $e) {
    // Недостаточно средств
    echo "Недостаточный баланс";
} catch (ApiException $e) {
    // Ошибка API (бизнес-логика)
    echo "Ошибка API: " . $e->getErrorCode() . " - " . $e->getMessage();
} catch (BingxException $e) {
    // Общие ошибки библиотеки
    echo "Ошибка BingX: " . $e->getMessage();
}
```

---

## 🧪 Тестирование

### Установить зависимости

```bash
composer install --dev
```

### Настроить окружение

```bash
cp tests/.env.example tests/.env
```

Заполнить `tests/.env`:

```env
BINGX_API_KEY=your_api_key_here
BINGX_API_SECRET=your_api_secret_here
BINGX_BASE_URI=https://open-api.bingx.com
BINGX_TEST_SYMBOL=BTC-USDT
BINGX_TEST_SYMBOL_SPOT=BTC-USDT
BINGX_TEST_LEVERAGE=10
BINGX_TEST_MARGIN=100
BINGX_TEST_QUANTITY=0.001
```

### Запустить тесты

```bash
# Только безопасные тесты (только чтение)
vendor/bin/phpunit

# Все тесты включая опасные операции
vendor/bin/phpunit --group dangerous

# Конкретные сервисы
vendor/bin/phpunit tests/Integration/MarketServiceTest.php
vendor/bin/phpunit tests/Integration/AccountServiceTest.php
vendor/bin/phpunit tests/Integration/TradeServiceTest.php
```

---

## 📊 Статистика библиотеки

### Полное покрытие API

| Сервис                       | Методов | Статус            |
|------------------------------|---------|-------------------|
| **USDT-M Perpetual Futures** |         |                   |
| Market Service               | 40      | ✅                 |
| TWAP Service                 | 7       | ✅                 |
| Account Service              | 40      | ✅                 |
| Trade Service                | 54      | ✅                 |
| Wallet Service               | 6       | ✅                 |
| Spot Account Service         | 9       | ✅                 |
| Sub-Account Service          | 20      | ✅                 |
| Copy Trading Service         | 13      | ✅                 |
| Contract Service             | 3       | ✅                 |
| Listen Key Service           | 3       | ✅                 |
| **Coin-M Perpetual Futures** |         |                   |
| Coin-M Market Service        | 6       | ✅                 |
| Coin-M Trade Service         | 17      | ✅                 |
| Coin-M Listen Key Service    | 3       | ✅                 |
| **Всего**                    | **220** | **100% покрытие** |

### Ключевые возможности

- ✅ Все Market API эндпоинты (v1, v2, v3)
- ✅ Quote API для оптимизированных рыночных данных
- ✅ TWAP ордера для алгоритмической торговли
- ✅ Полный Account API с управлением позициями
- ✅ Расширенный Trade API с продвинутыми функциями
- ✅ Wallet и Spot Account API
- ✅ WebSocket потоки данных

---

## 📖 Документация

- **BingX API** — [https://bingx-api.github.io/docs/](https://bingx-api.github.io/docs/)
- **GitHub репозиторий** — [https://github.com/tigusigalpa/bingx-php](https://github.com/tigusigalpa/bingx-php)
- **Проблемы и поддержка** — [GitHub Issues](https://github.com/tigusigalpa/bingx-php/issues)

---

## 🏷️ Версии

- **2.2.0** — Coin-M Perpetual Futures API (23 метода), поддержка контрактов с крипто-маржой
- **2.1.0** — Quote API, TWAP ордера, расширенные торговые функции, управление позициями (160+ методов)
- **2.0.0** — Полный рефакторинг: модульная архитектура, обработка ошибок, 100% покрытие API
- **1.0.0** — Базовая аутентификация и обёртки
- **0.1.0** — Первый релиз

---

## 👨‍💻 Автор

- **Igor Sazonov** (`@tigusigalpa`)
- **Email:** [sovletig@gmail.com](mailto:sovletig@gmail.com)
- **GitHub:** [github.com/tigusigalpa](https://github.com/tigusigalpa)

---

## 📄 Лицензия

MIT License — см. файл [LICENSE](LICENSE) для деталей.

---

## 🤝 Участие в разработке

Pull requests приветствуются! Пожалуйста, убедитесь что:

1. Код следует PSR-12
2. Добавлены тесты для новой функциональности
3. Обновлена документация

### Начало разработки

```bash
# Форкнуть репозиторий
git clone https://github.com/your-username/bingx-php.git
cd bingx-php

# Создать ветку функции
git checkout -b feature/YourFeature

# Внести изменения и добавить тесты
# Запустить тесты
vendor/bin/phpunit

# Закоммитить и отправить
git commit -m "Add your feature"
git push origin feature/YourFeature

# Открыть Pull Request
```

---

<div align="center">

**⭐ Если эта библиотека помогла вам, поставьте звезду на [GitHub](https://github.com/tigusigalpa/bingx-php)!**

**BingX PHP SDK** — полнофункциональный клиент для BingX API со 100% покрытием эндпоинтов и расширенными торговыми
возможностями.

</div>
