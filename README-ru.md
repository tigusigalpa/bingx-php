# BingX PHP SDK

<div align="center">
  <img src="https://github.com/user-attachments/assets/bc9acf4c-79c7-4e02-bb8d-75f2d8784b29" alt="BingX PHP SDK" style="max-width: 100%; height: auto;">
  
  [![PHP Version](https://img.shields.io/badge/php-%3E%3D8.1-blue?style=flat-square&logo=php)](https://www.php.net/)
  [![Composer](https://img.shields.io/badge/composer-v2-orange?style=flat-square&logo=composer)](https://getcomposer.org/)
  [![License](https://img.shields.io/badge/license-MIT-green?style=flat-square)](LICENSE)
  [![GitHub Stars](https://img.shields.io/github/stars/tigusigalpa/bingx-php?style=flat-square&logo=github)](https://github.com/tigusigalpa/bingx-php)
  [![Latest Release](https://img.shields.io/github/v/release/tigusigalpa/bingx-php?style=flat-square&logo=github)](https://github.com/tigusigalpa/bingx-php/releases)
  [![Test Coverage](https://img.shields.io/badge/coverage-119%2B%20tests-brightgreen?style=flat-square)](#-тестирование)
  
  **Полнофункциональный PHP SDK для BingX Swap V2 API**
  
  Торговля криптовалютами | Рыночные данные | WebSocket потоки | Laravel интеграция
</div>

---

## 📖 Оглавление

- [О библиотеке](#-о-библиотеке)
- [Возможности](#-возможности)
- [Быстрый старт](#-быстрый-старт)
- [Установка](#-установка)
- [Использование](#-использование)
  - [Market Service](#-market-service---рыночные-данные)
  - [Account Service](#-account-service---управление-аккаунтом)
  - [Trade Service](#-trade-service---торговые-операции)
  - [WebSocket API](#-websocket-api)
- [OrderBuilder](#-orderbuilder---продвинутое-создание-ордеров)
- [Обработка ошибок](#-обработка-ошибок)
- [Тестирование](#-тестирование)
- [Документация](#-документация)
- [Лицензия](#-лицензия)

---

## ✨ О библиотеке

**BingX PHP SDK** — это профессиональная, полнофункциональная библиотека для работы с BingX Swap V2 API. 

Создана с использованием современных практик PHP и обеспечивает:
- ✅ **100% покрытие** всех эндпоинтов BingX API
- ✅ **Модульная архитектура** с отдельными сервисами
- ✅ **Laravel 8-12 интеграция** с автодиспетчеризацией
- ✅ **Продвинутая обработка ошибок** с кастомными исключениями
- ✅ **WebSocket поддержка** для потоковых данных
- ✅ **Полная безопасность** с HMAC-SHA256 подписями
- ✅ **119+ методов** для полного контроля над торговлей

---

## 🚀 Возможности

### 📊 Поддерживаемые сервисы

| Сервис | Описание | Методов |
|--------|---------|--------|
| 🏪 **Market Service** | Рыночные данные, символы, цены, свечи, сделки | 28 |
| 👤 **Account Service** | Баланс, позиции, плечо, маржа, комиссии | 30 |
| 🔄 **Trade Service** | Ордера, история сделок, управление позициями | 41 |
| 💰 **Wallet Service** | Депозиты, выводы, адреса кошельков | 6 |
| 💵 **Spot Account Service** | Спотовый баланс, трансферы, внутренние переводы | 8 |
| 📋 **Contract Service** | Стандартные контракты API | 3 |
| 🔐 **Listen Key Service** | WebSocket аутентификация | 3 |

### 🛡️ Безопасность

- ✅ HMAC-SHA256 подпись всех запросов
- ✅ Автоматический timestamp с валидацией
- ✅ Поддержка base64 и hex кодирования подписи
- ✅ recvWindow для защиты от replay атак
- ✅ Кастомные исключения для разных типов ошибок

### 🔧 Удобство разработки

- ✅ Fluent интерфейс для построения ордеров
- ✅ IDE автодополнение с type hints
- ✅ Comprehensive error messages
- ✅ Full test coverage с примерами
- ✅ Поддержка чистого PHP и Laravel

---

## ⚡ Быстрый старт

### С Laravel

```php
// Получить текущую цену
$price = Bingx::market()->getLatestPrice('BTC-USDT');
echo "BTC price: {$price['price']}";

// Получить баланс
$balance = Bingx::account()->getBalance();

// Создать ордер через OrderBuilder
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

В корневой `composer.json` добавьте:

```json
{
  "repositories": [
    { "type": "path", "url": "packages/bingx-php" }
  ]
}
```

### Шаг 2: Установить пакет

```bash
composer require tigusigalpa/bingx-php:*
```

### Шаг 3: (Только для Laravel) Публиковать конфигурацию

```bash
php artisan vendor:publish --tag=bingx-config
```

### Шаг 4: Настроить переменные окружения

В `.env` добавьте:

```env
BINGX_API_KEY=your_api_key_here
BINGX_API_SECRET=your_api_secret_here
BINGX_SOURCE_KEY=optional_source_key
BINGX_BASE_URI=https://open-api.bingx.com
BINGX_SIGNATURE_ENCODING=base64
```

### 🔑 Создание API-ключей

1. Перейдите в [настройки API BingX](https://bingx.com/ru-ru/accounts/api)
2. Нажмите "Создать API"
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

// 24h статистика
$ticker = Bingx::market()->get24hrTicker('BTC-USDT');
$spotTicker = Bingx::market()->getSpot24hrTicker('BTC-USDT');

// Все символы (в одном запросе)
$allTickers = Bingx::market()->get24hrTicker();
```

#### Глубина рынка и свечи

```php
// Глубина рынка (order book)
$depth = Bingx::market()->getDepth('BTC-USDT', 20);
$spotDepth = Bingx::market()->getSpotDepth('BTC-USDT', 20);

// Свечи (candlesticks)
$klines = Bingx::market()->getKlines('BTC-USDT', '1h', 100);
$spotKlines = Bingx::market()->getSpotKlines('BTC-USDT', '1h', 100);

// С временным диапазоном
$klines = Bingx::market()->getKlines(
    'BTC-USDT', '1h', 100,
    strtotime('2024-01-01') * 1000,
    strtotime('2024-01-02') * 1000
);
```

#### Финансирование и марка

```php
// Ставка финансирования
$fundingRate = Bingx::market()->getFundingRateHistory('BTC-USDT', 100);

// Марка-цена
$markPrice = Bingx::market()->getMarkPrice('BTC-USDT');

// Премиум индекс
$premiumKlines = Bingx::market()->getPremiumIndexKlines('BTC-USDT', '1h', 100);

// Непрерывные контракты
$continuousKlines = Bingx::market()->getContinuousKlines('BTC-USDT', '1h', 100);
```

#### Сделки

```php
// Агрегированные сделки
$aggTrades = Bingx::market()->getAggregateTrades('BTC-USDT', 500);

// Последние сделки
$recentTrades = Bingx::market()->getRecentTrades('BTC-USDT', 500);

// Спотовые сделки
$spotAggTrades = Bingx::market()->getSpotAggregateTrades('BTC-USDT', 500);
$spotRecentTrades = Bingx::market()->getSpotRecentTrades('BTC-USDT', 500);
```

#### Анализ настроений рынка

```php
// Соотношение лонг/шорт позиций
$longShortRatio = Bingx::market()->getTopLongShortRatio('BTC-USDT', 10);

// История соотношений
$historicalRatio = Bingx::market()->getHistoricalTopLongShortRatio(
    'BTC-USDT', 500,
    strtotime('2024-01-01') * 1000,
    strtotime('2024-01-02') * 1000
);

// Базисные данные контрактов
$basis = Bingx::market()->getBasis('BTC-USDT', 'PERPETUAL', 100);
```

---

### 👤 Account Service - Управление аккаунтом

#### Баланс и позиции

```php
// Получить баланс
$balance = Bingx::account()->getBalance();

// Все позиции
$allPositions = Bingx::account()->getPositions();

// Позиции для конкретного символа
$positions = Bingx::account()->getPositions('BTC-USDT');

// Информация об аккаунте
$accountInfo = Bingx::account()->getAccountInfo();
```

#### Управление плечом и маржей

```php
// Получить текущее плечо
$leverage = Bingx::account()->getLeverage('BTC-USDT');

// Установить плечо
Bingx::account()->setLeverage('BTC-USDT', 'BOTH', 20);

// Режим маржи (ISOLATED или CROSSED)
$marginMode = Bingx::account()->getMarginMode('BTC-USDT');
Bingx::account()->setMarginMode('BTC-USDT', 'ISOLATED');

// Маржа позиции
Bingx::account()->setPositionMargin('BTC-USDT', 'LONG', 100.0, 1);
```

#### Торговые комиссии

```php
// Комиссии для символа
$fees = Bingx::account()->getTradingFees('BTC-USDT');

// Права доступа API
$permissions = Bingx::account()->getAccountPermissions();

// Информация об API ключе
$apiKey = Bingx::account()->getApiKey();

// Комиссии пользователя
$userRates = Bingx::account()->getUserCommissionRates('BTC-USDT');

// Лимиты API
$rateLimits = Bingx::account()->getApiRateLimits();
```

#### История операций

```php
// История баланса
$balanceHistory = Bingx::account()->getBalanceHistory('USDT', 100);

// История депозитов
$deposits = Bingx::account()->getDepositHistory('USDT', 100);

// История выводов
$withdrawals = Bingx::account()->getWithdrawHistory('USDT', 100);
```

#### Управление активами

```php
// Детали актива
$assetDetails = Bingx::account()->getAssetDetails('USDT');

// Все доступные активы
$allAssets = Bingx::account()->getAllAssets();

// Финансирование кошелька
$fundingWallet = Bingx::account()->getFundingWallet('USDT');

// Конвертация dust (мелких активов)
Bingx::account()->dustTransfer(['BTC', 'ETH']);
```

---

### 🔄 Trade Service - Торговые операции

#### Быстрые торговые методы

```php
// Спотовые ордера
$buy = Bingx::trade()->spotMarketBuy('BTC-USDT', 0.001);
$sell = Bingx::trade()->spotMarketSell('BTC-USDT', 0.001);

// Спотовые лимит-ордера
$limitBuy = Bingx::trade()->spotLimitBuy('BTC-USDT', 0.001, 50000);
$limitSell = Bingx::trade()->spotLimitSell('BTC-USDT', 0.001, 60000);

// Фьючерсные ордера
$longOrder = Bingx::trade()->futuresLongMarket('BTC-USDT', 100, 10);
$shortOrder = Bingx::trade()->futuresShortMarket('BTC-USDT', 100, 10);

// С защитными ордерами
$longLimit = Bingx::trade()->futuresLongLimit(
    'BTC-USDT', 100, 50000, 48000, 55000, 10
);
```

#### Создание и управление ордерами

```php
// Создать ордер
$order = Bingx::trade()->createOrder([
    'symbol' => 'BTC-USDT',
    'side' => 'BUY',
    'type' => 'MARKET',
    'quantity' => 0.001
]);

// Тестовый ордер (без исполнения)
$testOrder = Bingx::trade()->createTestOrder([
    'symbol' => 'BTC-USDT',
    'side' => 'BUY',
    'type' => 'LIMIT',
    'quantity' => 0.001,
    'price' => 50000
]);

// Пакетное создание
$batchOrders = Bingx::trade()->createBatchOrders([
    [
        'symbol' => 'BTC-USDT',
        'side' => 'BUY',
        'type' => 'LIMIT',
        'quantity' => 0.001,
        'price' => 50000
    ],
    [
        'symbol' => 'ETH-USDT',
        'side' => 'SELL',
        'type' => 'LIMIT',
        'quantity' => 0.01,
        'price' => 3000
    ]
]);
```

#### Отмена ордеров

```php
// Отменить конкретный ордер
Bingx::trade()->cancelOrder('BTC-USDT', '123456789');

// Отменить все ордера для символа
Bingx::trade()->cancelAllOrders('BTC-USDT');

// Пакетная отмена
Bingx::trade()->cancelBatchOrders('BTC-USDT', ['123456789', '987654321']);

// Отмена и замена
Bingx::trade()->cancelAndReplaceOrder(
    'BTC-USDT', '123456789', 'BUY', 'LIMIT', 0.001, 50000
);
```

#### Получение информации об ордерах

```php
// Детали ордера
$order = Bingx::trade()->getOrder('BTC-USDT', '123456789');

// Открытые ордера
$openOrders = Bingx::trade()->getOpenOrders();
$openOrdersForSymbol = Bingx::trade()->getOpenOrders('BTC-USDT', 50);

// История ордеров
$orderHistory = Bingx::trade()->getOrderHistory('BTC-USDT', 100);

// История сделок
$userTrades = Bingx::trade()->getUserTrades('BTC-USDT', 100);
```

#### Расчет комиссий

```php
// Расчет комиссии для фьючерса
$commission = Bingx::trade()->calculateFuturesCommission(100, 10);
// Возвращает детальную информацию о комиссии

// Быстрый расчет суммы
$amount = Bingx::trade()->getCommissionAmount(100, 10); // 0.45

// Пакетный расчет
$batchCommission = Bingx::trade()->calculateBatchCommission([
    ['margin' => 100, 'leverage' => 10],
    ['margin' => 200, 'leverage' => 5]
]);

// Получить ставки комиссии
$rates = Bingx::trade()->getCommissionRates();
```

#### Управление позициями

```php
// Режим позиции
$positionMode = Bingx::trade()->getPositionMode();
Bingx::trade()->setPositionMode('HEDGE_MODE');

// Сторона позиции
$positionSide = Bingx::trade()->getPositionSide();
Bingx::trade()->setPositionSide('BOTH');

// Закрыть все позиции
Bingx::trade()->closeAllPositions('BTC-USDT');

// Тип маржи
$marginType = Bingx::trade()->getMarginType('BTC-USDT');
Bingx::trade()->changeMarginType('BTC-USDT', 'ISOLATED');
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

// Адрес депозита
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

// Универсальный трансфер
$transfer = Bingx::spotAccount()->universalTransfer(
    type: 'FUND_PFUTURES',
    asset: 'USDT',
    amount: 100.0
);

// История трансферов
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
```

---

### 🌐 WebSocket API

#### Установка зависимости

```bash
composer require textalk/websocket
```

#### Market Data Stream (публичные данные)

```php
use Tigusigalpa\BingX\WebSocket\MarketDataStream;

$stream = new MarketDataStream();
$stream->connect();

// Подписаться на события
$stream->subscribeTrade('BTC-USDT');
$stream->subscribeKline('BTC-USDT', '1m');
$stream->subscribeDepth('BTC-USDT', 20);
$stream->subscribeTicker('BTC-USDT');
$stream->subscribeBookTicker('BTC-USDT');

// Обработка сообщений
$stream->onMessage(function ($data) {
    echo "Данные: " . json_encode($data) . PHP_EOL;
    
    if (isset($data['dataType'])) {
        switch ($data['dataType']) {
            case 'BTC-USDT@trade':
                echo "Новая сделка: {$data['data']['p']}";
                break;
            case 'BTC-USDT@kline_1m':
                echo "Новая свеча";
                break;
        }
    }
});

// Начать прослушивание
$stream->listen();

// Отписаться и закрыть
$stream->unsubscribeTrade('BTC-USDT');
$stream->disconnect();
```

#### Account Data Stream (приватные данные)

```php
use Tigusigalpa\BingX\WebSocket\AccountDataStream;

// 1. Получить Listen Key
$response = Bingx::listenKey()->generate();
$listenKey = $response['listenKey'];

// 2. Создать подключение
$stream = new AccountDataStream($listenKey);
$stream->connect();

// 3. Слушать обновления
$stream->onBalanceUpdate(function ($balances) {
    foreach ($balances as $balance) {
        echo "Баланс {$balance['a']}: {$balance['wb']}";
    }
});

$stream->onPositionUpdate(function ($positions) {
    foreach ($positions as $position) {
        echo "Позиция {$position['s']}: {$position['pa']}";
    }
});

$stream->onOrderUpdate(function ($order) {
    echo "Ордер #{$order['i']}: {$order['X']}";
});

// 4. Начать прослушивание
$stream->listen();

// 5. Продлить Listen Key (каждые 30 минут)
Bingx::listenKey()->extend($listenKey);

// 6. Закрыть подключение
Bingx::listenKey()->delete($listenKey);
$stream->disconnect();
```

#### Управление Listen Key

```php
// Создать новый ключ (действителен 60 минут)
$response = Bingx::listenKey()->generate();
$listenKey = $response['listenKey'];

// Продлить срок действия (рекомендуется каждые 30 минут)
Bingx::listenKey()->extend($listenKey);

// Удалить ключ
Bingx::listenKey()->delete($listenKey);
```

---

## 🎯 OrderBuilder - Продвинутое создание ордеров

OrderBuilder предоставляет удобный fluent интерфейс для создания сложных ордеров с автоматическим расчетом.

### Простые примеры

```php
// Фьючерсный ордер с плечом
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

// Спотовый лимит-ордер
$order = Bingx::trade()->order()
    ->spot()
    ->symbol('ETH-USDT')
    ->sell()
    ->type('LIMIT')
    ->quantity(0.1)
    ->price(3000)
    ->execute();
```

### Ордера с защитными стопами

```php
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
    ->stopLossPercent(5)      // Stop Loss на 5% ниже
    ->takeProfitPercent(15)   // Take Profit на 15% выше
    ->execute();

// Шорт с фиксированными ценами
$order = Bingx::trade()->order()
    ->futures()
    ->symbol('BTC-USDT')
    ->sell()
    ->short()
    ->type('MARKET')
    ->margin(150)
    ->leverage(5)
    ->stopLossPrice(52000)    // Фиксированный Stop Loss
    ->takeProfitPrice(45000)  // Фиксированный Take Profit
    ->execute();
```

### Расширенные параметры

```php
$order = Bingx::trade()->order()
    ->futures()
    ->symbol('BTC-USDT')
    ->buy()
    ->long()
    ->type('STOP_MARKET')
    ->margin(150)
    ->leverage(10)
    ->stopPrice(48000)              // Триггер для входа
    ->stopGuaranteed()              // Гарантированный стоп
    ->stopLoss(47000)               // Защитный стоп-лосс
    ->takeProfit(51000)             // Тейк-профит
    ->reduceOnly()                  // Не увеличивать позицию
    ->clientOrderId('strategy-001')
    ->workingType('MARK_PRICE')
    ->newOrderRespType('FULL')
    ->recvWindow(5000)
    ->execute();
```

### Доступные методы OrderBuilder

| Метод | Описание | Применение |
|-------|---------|-----------|
| `spot()` / `futures()` | Тип рынка | Обязательно |
| `symbol('BTC-USDT')` | Торговая пара | Обязательно |
| `buy()` / `sell()` | Направление | Обязательно |
| `type('MARKET\|LIMIT\|STOP')` | Тип ордера | Обязательно |
| `long()` / `short()` | Позиция | Фьючерсы |
| `leverage(10)` | Плечо (1-125) | Фьючерсы |
| `quantity(0.001)` | Размер | Спот |
| `margin(100)` | Маржа | Фьючерсы |
| `price(50000)` | Цена | LIMIT/STOP |
| `stopLoss(49000)` | Стоп-лосс (цена) | Фьючерсы |
| `stopLossPercent(5)` | Стоп-лосс (%) | Фьючерсы |
| `takeProfit(52000)` | Тейк-профит (цена) | Фьючерсы |
| `takeProfitPercent(10)` | Тейк-профит (%) | Фьючерсы |
| `clientOrderId('id')` | Пользовательский ID | Все типы |
| `timeInForce('GTC')` | Время жизни (GTC/IOC/FOK) | LIMIT/STOP |
| `reduceOnly()` | Только закрытие позиции | Фьючерсы |
| `stopPrice(48000)` | Триггер-цена | Условные ордера |
| `workingType('MARK_PRICE')` | Тип триггера | Фьючерсы |
| `newOrderRespType('FULL')` | Формат ответа | Все типы |
| `test()` | Тестовый ордер | Все типы |

---

## ⚠️ Обработка ошибок

Библиотека предоставляет кастомные исключения для разных типов ошибок:

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
    echo "Auth error: " . $e->getMessage();
} catch (RateLimitException $e) {
    // Превышен лимит запросов
    echo "Rate limit exceeded. Retry after: " . $e->getRetryAfter();
} catch (InsufficientBalanceException $e) {
    // Недостаточно средств
    echo "Insufficient balance";
} catch (ApiException $e) {
    // Ошибка API (бизнес-логика)
    echo "API error: " . $e->getErrorCode() . " - " . $e->getMessage();
} catch (BingxException $e) {
    // Общие ошибки библиотеки
    echo "BingX error: " . $e->getMessage();
}
```

---

## 🧪 Тестирование

### Установка зависимостей

```bash
composer install --dev
```

### Настройка окружения

```bash
cp tests/.env.example tests/.env
```

Заполните `tests/.env`:

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

### Запуск тестов

```bash
# Только безопасные тесты (чтение данных)
vendor/bin/phpunit

# Все тесты включая опасные операции
vendor/bin/phpunit --group dangerous

# Конкретные сервисы
vendor/bin/phpunit tests/Integration/MarketServiceTest.php
vendor/bin/phpunit tests/Integration/AccountServiceTest.php
vendor/bin/phpunit tests/Integration/TradeServiceTest.php

# Только Unit тесты
vendor/bin/phpunit tests/Unit/
```

### Типы тестов

- **Unit тесты** — проверяют базовую функциональность без API вызовов
- **Integration тесты** — проверяют реальные эндпоинты BingX API
- **Safe тесты** (`@group safe`) — только чтение данных
- **Dangerous тесты** (`@group dangerous`) — операции, изменяющие данные

---

## 📊 Статистика библиотеки

### Полное покрытие API

| Сервис | Методов | Статус |
|--------|--------|--------|
| Market Service | 28 | ✅ |
| Account Service | 30 | ✅ |
| Trade Service | 41 | ✅ |
| Wallet Service | 6 | ✅ |
| Spot Account Service | 8 | ✅ |
| Contract Service | 3 | ✅ |
| Listen Key Service | 3 | ✅ |
| **Всего** | **119+** | **100% покрытие** |

### Ключевые особенности

- ✅ Все эндпоинты Market API
- ✅ Полный Account API
- ✅ Расширенный Trade API
- ✅ Wallet и Spot Account API
- ✅ WebSocket потоки данных
- ✅ OrderBuilder для сложных ордеров
- ✅ Анализ настроений рынка
- ✅ Управление активами и dust конвертация
- ✅ Исторические данные и статистика
- ✅ Полная безопасность и обработка ошибок

---

## 📖 Документация

- **BingX API** — [https://bingx-api.github.io/docs/](https://bingx-api.github.io/docs/)
- **GitHub репозиторий** — [https://github.com/tigusigalpa/bingx-php](https://github.com/tigusigalpa/bingx-php)
- **Issues & Support** — [GitHub Issues](https://github.com/tigusigalpa/bingx-php/issues)

---

## 🏷️ Версии

- **2.0.0** — Полный рефакторинг: модульная архитектура, обработка ошибок, 100% API покрытие
- **1.0.0** — Базовая авторизация и обертки
- **0.1.0** — Первоначальная версия

---

## 👨‍💻 Автор

- **Igor Sazonov** (`@tigusigalpa`)
- **Email:** [sovletig@gmail.com](mailto:sovletig@gmail.com)
- **GitHub:** [github.com/tigusigalpa](https://github.com/tigusigalpa)

---

## 📄 Лицензия

MIT License — см. файл [LICENSE](LICENSE) для деталей.

---

## 🤝 Вклад в разработку

Pull requests приветствуются! Пожалуйста, убедитесь, что:

1. Код соответствует PSR-12
2. Добавлены тесты для новой функциональности
3. Обновлена документация

### Как начать разработку

```bash
# Fork репозиторий
git clone https://github.com/your-username/bingx-php.git
cd bingx-php

# Создать feature branch
git checkout -b feature/YourFeature

# Внести изменения и тесты
# Запустить тесты
vendor/bin/phpunit

# Commit и push
git commit -m "Add your feature"
git push origin feature/YourFeature

# Открыть Pull Request
```

---

<div align="center">

**⭐ Если эта библиотека помогла вам, поставьте звезду на [GitHub](https://github.com/tigusigalpa/bingx-php)!**

**BingX PHP SDK** — Полнофункциональный клиент для BingX API с 100% покрытием эндпоинтов и продвинутыми возможностями торговли.

</div>
