# BingX PHP — клиент Swap V2 API с интеграцией Laravel 8–12

<div align="center">
  <img src="https://github.com/user-attachments/assets/bc9acf4c-79c7-4e02-bb8d-75f2d8784b29" alt="BingX PHP SDK" style="max-width: 100%; height: auto;">
</div>

Профессиональная библиотека для работы с BingX Swap V2 API с полной поддержкой всех эндпоинтов: маркет-данные,
управление аккаунтом, торговые операции. Включает модульную архитектуру, продвинутую обработку ошибок и полную
интеграцию с Laravel 8–12.

## 🔑 Создание API-ключей

**Перед началом работы необходимо создать API-ключи в вашем аккаунте BingX:**

1. Перейдите в настройки API: [https://bingx.com/ru-ru/accounts/api](https://bingx.com/ru-ru/accounts/api)
2. Нажмите "Создать API"
3. Сохраните **API Key** и **Secret Key** в безопасном месте
4. Настройте права доступа
5. При необходимости добавьте IP-адреса для дополнительной безопасности

⚠️ **Важно:** Secret Key отображается только один раз при создании. Сохраните его надежно!

## ✨ Возможности

### 🏗️ Модульная архитектура

- **Market Service** - работа с рыночными данными (символы, цены, глубина, свечи)
- **Account Service** - управление аккаунтом (баланс, позиции, кредитное плечо)
- **Trade Service** - торговые операции (ордера, история, управление позициями)
- **BaseHttpClient** - основа для HTTP запросов с HMAC-SHA256 подписью

### 🛡️ Безопасность и обработка ошибок

- HMAC-SHA256 подпись запросов с автоматическим timestamp
- Кастомные исключения для разных типов ошибок API
- Валидация ответов и детальная обработка HTTP ошибок
- Поддержка `base64` и `hex` кодирования подписи

### 🚀 Интеграция с Laravel

- Автообнаружение сервис-провайдера и фасада
- Публикуемый конфигурационный файл
- Поддержка dependency injection
- Обратная совместимость с существующим кодом

## 📦 Установка

1. Добавьте репозиторий в корневой `composer.json` вашего проекта:
   ```json
   {
     "repositories": [
       { "type": "path", "url": "public_html/packages/bingx-php" }
     ]
   }
   ```

2. Установите пакет:
   ```bash
   composer require tigusigalpa/bingx-php:* --prefer-source
   ```

## 🔧 Интеграция с Laravel

### Публикация конфигурации

```bash
php artisan vendor:publish --tag=bingx-config
```

### Настройка окружения

Добавьте в `.env` файл:

```env
BINGX_API_KEY=your_api_key
BINGX_API_SECRET=your_api_secret
BINGX_SOURCE_KEY=optional_source_key
BINGX_BASE_URI=https://open-api.bingx.com
BINGX_SIGNATURE_ENCODING=base64
```

## 🧩 Использование без Laravel (чистый PHP)

Пакет можно использовать как обычную Composer-библиотеку, без Laravel.

### Установка через Composer

Если пакет опубликован на Packagist:

```bash
composer require tigusigalpa/bingx-php
```

Если вы используете локальный `path`‑репозиторий:

```jsonc
// composer.json
{
  "require": {
    "tigusigalpa/bingx-php": "*"
  },
  "repositories": [
    { "type": "path", "url": "public_html/packages/bingx-php" }
  ]
}
```

Затем:

```bash
composer update tigusigalpa/bingx-php --prefer-source
```

### Инициализация клиента в чистом PHP

```php
<?php

require __DIR__ . '/vendor/autoload.php';

use Tigusigalpa\BingX\BingxClient;
use Tigusigalpa\BingX\Http\BaseHttpClient;

$apiKey    = 'YOUR_API_KEY';
$apiSecret = 'YOUR_API_SECRET';
$baseUri   = 'https://open-api.bingx.com';

// Базовый HTTP‑клиент
$http = new BaseHttpClient($apiKey, $apiSecret, $baseUri);

// Основной клиент BingX
$bingx = new BingxClient($http);

// Market data
$symbols = $bingx->market()->getFuturesSymbols();
$price   = $bingx->market()->getLatestPrice('BTC-USDT');

// Account
$balance     = $bingx->account()->getBalance();
$leverage    = $bingx->account()->getLeverage('BTC-USDT');
$setLeverage = $bingx->account()->setLeverage('BTC-USDT', 'BOTH', 10);

// Trading
$order = $bingx->trade()->spotMarketBuy('BTC-USDT', 0.001);
```

### OrderBuilder в чистом PHP

```php
$order = $bingx->trade()->order()
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

## 📚 Использование

### 🏪 Market Service - Рыночные данные

#### 📋 Торговые пары и символы

```php
// Получить все доступные символы (спот + фьючерсы)
$allSymbols = Bingx::market()->getAllSymbols();
// Возвращает: ['spot' => [...], 'futures' => [...]]

// Получить только спотовые торговые пары
$spotSymbols = Bingx::market()->getSpotSymbols();

// Получить только фьючерсные символы
$futuresSymbols = Bingx::market()->getFuturesSymbols();

// Обратная совместимость (только фьючерсы)
$symbols = Bingx::market()->getSymbols();
```

#### 💰 Цены и статистика

```php
// Текущая цена фьючерса
$futuresPrice = Bingx::market()->getLatestPrice('BTC-USDT');

// Текущая цена спота
$spotPrice = Bingx::market()->getSpotLatestPrice('BTC-USDT');

// 24-часовая статистика фьючерсов
$futuresTicker = Bingx::market()->get24hrTicker('BTC-USDT');

// 24-часовая статистика спота
$spotTicker = Bingx::market()->getSpot24hrTicker('BTC-USDT');

// Статистика всех символов
$allTickers = Bingx::market()->get24hrTicker(); // фьючерсы
$allSpotTickers = Bingx::market()->getSpot24hrTicker(); // спот
```

#### 📊 Глубина рынка и свечи

```php
// Глубина рынка фьючерсов
$futuresDepth = Bingx::market()->getDepth('BTC-USDT', 20);

// Глубина рынка спота
$spotDepth = Bingx::market()->getSpotDepth('BTC-USDT', 20);

// Свечи фьючерсов
$futuresKlines = Bingx::market()->getKlines('BTC-USDT', '1h', 100);

// Свечи спота
$spotKlines = Bingx::market()->getSpotKlines('BTC-USDT', '1h', 100);

// Свечи с временным диапазоном
$klines = Bingx::market()->getKlines('BTC-USDT', '1h', 100, 
    strtotime('2024-01-01') * 1000, 
    strtotime('2024-01-02') * 1000
);
```

#### 📈 Финансирование и марка

```php
// История ставки финансирования
$fundingRate = Bingx::market()->getFundingRateHistory('BTC-USDT', 100);

// Текущая марка-цена
$markPrice = Bingx::market()->getMarkPrice('BTC-USDT');

// Премиум индекс свечи
$premiumKlines = Bingx::market()->getPremiumIndexKlines('BTC-USDT', '1h', 100);

// Непрерывные контракты свечи
$continuousKlines = Bingx::market()->getContinuousKlines('BTC-USDT', '1h', 100);

// Индексная цена свечи
$indexPriceKlines = Bingx::market()->getIndexPriceKlines('BTC-USDT', '1h', 100);
```

#### 🔄 Сделки и агрегированные данные

```php
// Агрегированные сделки фьючерсов
$aggTrades = Bingx::market()->getAggregateTrades('BTC-USDT', 500);

// Последние сделки фьючерсов
$recentTrades = Bingx::market()->getRecentTrades('BTC-USDT', 500);

// Агрегированные сделки спота
$spotAggTrades = Bingx::market()->getSpotAggregateTrades('BTC-USDT', 500);

// Последние сделки спота
$spotRecentTrades = Bingx::market()->getSpotRecentTrades('BTC-USDT', 500);

// Сделки с временным диапазоном и ID
$trades = Bingx::market()->getAggregateTrades('BTC-USDT', 500, 12345,
    strtotime('2024-01-01') * 1000,
    strtotime('2024-01-02') * 1000
);
```

#### ⏰ Время сервера

```php
// Время сервера фьючерсов
$futuresTime = Bingx::market()->getServerTime();

// Время сервера спота
$spotTime = Bingx::market()->getSpotServerTime();
```

#### 📊 Анализ настроений рынка

```php
// Соотношение лонг/шорт позиций
$longShortRatio = Bingx::market()->getTopLongShortRatio('BTC-USDT', 10);

// Соотношение позиций топ трейдеров
$topTradersRatio = Bingx::market()->getTopTradersPositionRatio('BTC-USDT', 10);

// История соотношений аккаунтов
$historicalRatio = Bingx::market()->getHistoricalTopLongShortRatio('BTC-USDT', 500,
    strtotime('2024-01-01') * 1000,
    strtotime('2024-01-02') * 1000
);

// История соотношений позиций
$positionRatio = Bingx::market()->getTopTradersLongShortRatio('BTC-USDT', 500,
    strtotime('2024-01-01') * 1000,
    strtotime('2024-01-02') * 1000
);

// Базисные данные контрактов
$basis = Bingx::market()->getBasis('BTC-USDT', 'PERPETUAL', 100,
    strtotime('2024-01-01') * 1000,
    strtotime('2024-01-02') * 1000
);
```

### 👤 Account Service - Управление аккаунтом

#### 💰 Баланс и позиции

```php
// Получить баланс аккаунта
$balance = Bingx::account()->getBalance();

// Получить все позиции
$allPositions = Bingx::account()->getPositions();

// Получить позиции для конкретного символа
$positions = Bingx::account()->getPositions('BTC-USDT');

// Информация об аккаунте
$accountInfo = Bingx::account()->getAccountInfo();
```

#### 📊 Торговые комиссии и маржа

```php
// Торговые комиссии для символа
$fees = Bingx::account()->getTradingFees('BTC-USDT');

// Получить режим маржи
$marginMode = Bingx::account()->getMarginMode('BTC-USDT');

// Установить режим маржи (ISOLATED, CROSSED)
Bingx::account()->setMarginMode('BTC-USDT', 'ISOLATED');

// Получить маржу позиции
$positionMargin = Bingx::account()->getPositionMargin('BTC-USDT');

// Установить маржу позиции
Bingx::account()->setPositionMargin('BTC-USDT', 'LONG', 100.0, 1);
```

#### ⚙️ Управление плечом

```php
// Получить текущее плечо
$leverage = Bingx::account()->getLeverage('BTC-USDT');

// Установить плечо
Bingx::account()->setLeverage('BTC-USDT', 'BOTH', 20);

// С recvWindow для безопасности
$leverage = Bingx::account()->getLeverage('BTC-USDT', 5000);
Bingx::account()->setLeverage('BTC-USDT', 'BOTH', 20, 5000);
```

#### 📈 История операций

```php
// История баланса
$balanceHistory = Bingx::account()->getBalanceHistory('USDT', 100);

// История депозитов
$depositHistory = Bingx::account()->getDepositHistory('USDT', 100,
    strtotime('2024-01-01') * 1000,
    strtotime('2024-01-02') * 1000
);

// История выводов
$withdrawHistory = Bingx::account()->getWithdrawHistory('USDT', 100,
    strtotime('2024-01-01') * 1000,
    strtotime('2024-01-02') * 1000
);
```

#### 🔐 API и безопасность

```php
// Права доступа API
$permissions = Bingx::account()->getAccountPermissions();

// Информация об API ключе
$apiKey = Bingx::account()->getApiKey();

// Комиссии пользователя
$userCommissionRates = Bingx::account()->getUserCommissionRates('BTC-USDT');

// Права API ключа
$apiKeyPermissions = Bingx::account()->getApiKeyPermissions();

// Лимиты API
$rateLimits = Bingx::account()->getApiRateLimits();
```

#### 💼 Управление активами

```php
// Детали актива
$assetDetails = Bingx::account()->getAssetDetails('USDT');

// Все доступные активы
$allAssets = Bingx::account()->getAllAssets();

// История универсальных трансферов
$transferHistory = Bingx::account()->getUniversalTransferHistory('UMFUTURE_MAIN', 100,
    strtotime('2024-01-01') * 1000,
    strtotime('2024-01-02') * 1000
);

// Создать универсальный трансфер
Bingx::account()->createUniversalTransfer('MAIN_UMFUTURE', 'USDT', 100.0);

// Финансирование кошелька
$fundingWallet = Bingx::account()->getFundingWallet('USDT');
```

#### 🧹 Управление мелкими активами (Dust)

```php
// История конвертации dust
$dustLog = Bingx::account()->getDustLog(100,
    strtotime('2024-01-01') * 1000,
    strtotime('2024-01-02') * 1000
);

// Конвертировать dust в BNB
Bingx::account()->dustTransfer(['BTC', 'ETH', 'LTC']);
```

#### 💰 Дивиденды и доходы

```php
// История дивидендов активов
$dividendRecord = Bingx::account()->getAssetDividendRecord('BNB', 100,
    strtotime('2024-01-01') * 1000,
    strtotime('2024-01-02') * 1000
);
```

#### ⚡ Быстрый вывод

```php
// Включить быстрый вывод
Bingx::account()->enableFastWithdrawSwitch();

// Выключить быстрый вывод
Bingx::account()->disableFastWithdrawSwitch();
```

### 🔄 Trade Service - Торговые операции

#### 📋 Создание ордеров

```php
// Создать ордер через параметры
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

// Пакетное создание ордеров
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

#### 🎯 Быстрые торговые методы

```php
// Спотовые рыночные ордера
$buyOrder = Bingx::trade()->spotMarketBuy('BTC-USDT', 0.001);
$sellOrder = Bingx::trade()->spotMarketSell('BTC-USDT', 0.001);

// Спотовые лимитные ордера
$limitBuy = Bingx::trade()->spotLimitBuy('BTC-USDT', 0.001, 50000);
$limitSell = Bingx::trade()->spotLimitSell('BTC-USDT', 0.001, 60000);

// Фьючерсные рыночные ордера
$longOrder = Bingx::trade()->futuresLongMarket('BTC-USDT', 100, 10);
$shortOrder = Bingx::trade()->futuresShortMarket('BTC-USDT', 100, 10);

// Фьючерсные лимитные ордера со стоп-лосс и тейк-профит
$longLimit = Bingx::trade()->futuresLongLimit('BTC-USDT', 100, 50000, 48000, 55000, 10);
$shortLimit = Bingx::trade()->futuresShortLimit('BTC-USDT', 100, 50000, 52000, 45000, 10);
```

#### 📊 Управление ордерами

```php
// Получить детали ордера
$order = Bingx::trade()->getOrder('BTC-USDT', '123456789');

// Получить открытые ордера
$openOrders = Bingx::trade()->getOpenOrders();
$openOrdersForSymbol = Bingx::trade()->getOpenOrders('BTC-USDT', 50);

// История ордеров
$orderHistory = Bingx::trade()->getOrderHistory('BTC-USDT', 100,
    strtotime('2024-01-01') * 1000,
    strtotime('2024-01-02') * 1000
);

// Исполненные ордера
$filledOrders = Bingx::trade()->getFilledOrders('BTC-USDT', 100,
    strtotime('2024-01-01') * 1000,
    strtotime('2024-01-02') * 1000
);
```

#### ❌ Отмена ордеров

```php
// Отменить конкретный ордер
$cancelled = Bingx::trade()->cancelOrder('BTC-USDT', '123456789');

// Отменить все ордера для символа
$allCancelled = Bingx::trade()->cancelAllOrders('BTC-USDT');

// Пакетная отмена ордеров
$batchCancelled = Bingx::trade()->cancelBatchOrders('BTC-USDT', ['123456789', '987654321']);

// Отмена и замена ордера
$replaced = Bingx::trade()->cancelAndReplaceOrder('BTC-USDT', '123456789', 'BUY', 'LIMIT', 0.001, 50000);
```

#### 📈 История сделок

```php
// Сделки пользователя
$userTrades = Bingx::trade()->getUserTrades('BTC-USDT', 100,
    strtotime('2024-01-01') * 1000,
    strtotime('2024-01-02') * 1000
);

// Исторические сделки рынка
$historicalTrades = Bingx::trade()->getHistoricalTrades('BTC-USDT', 500, 12345,
    strtotime('2024-01-01') * 1000,
    strtotime('2024-01-02') * 1000
);

// Сделки аккаунта
$accountTrades = Bingx::trade()->getAccountTrades('BTC-USDT', 500, 12345,
    strtotime('2024-01-01') * 1000,
    strtotime('2024-01-02') * 1000
);

// Список последних сделок
$recentTradesList = Bingx::trade()->getRecentTradesList('BTC-USDT', 500, 12345);
```

#### ⚙️ Управление плечом и маржей

```php
// Изменить плечо
Bingx::trade()->changeLeverage('BTC-USDT', 'BOTH', 20);
Bingx::trade()->changeLeverage('BTC-USDT', 'BOTH', 20, 5000); // с recvWindow

// Изменить тип маржи
Bingx::trade()->changeMarginType('BTC-USDT', 'ISOLATED');

// Изменить маржу позиции
Bingx::trade()->modifyPositionMargin('BTC-USDT', 'LONG', 100.0, 1);
```

#### 🎯 Управление позициями

```php
// Получить режим позиции
$positionMode = Bingx::trade()->getPositionMode();

// Установить режим позиции
Bingx::trade()->setPositionMode('HEDGE_MODE');

// Получить сторону позиции
$positionSide = Bingx::trade()->getPositionSide();

// Установить сторону позиции
Bingx::trade()->setPositionSide('BOTH');
```

#### 📊 Анализ и статистика

```php
// Средняя цена
$avgPrice = Bingx::trade()->getAvgPrice('BTC-USDT', 'BUY', 'MARKET', 0.001);
$avgPriceLimit = Bingx::trade()->getAvgPrice('BTC-USDT', 'BUY', 'LIMIT', 0.001, 50000);

// Комиссии из API
$commissionRates = Bingx::trade()->getApiCommissionRates('BTC-USDT');

// История модификации ордеров
$modificationHistory = Bingx::trade()->getOrderModificationHistory('BTC-USDT', 100,
    strtotime('2024-01-01') * 1000,
    strtotime('2024-01-02') * 1000
);

// Изменение существующего LIMIT-ордера (только quantity)
// По orderId
$modified = Bingx::trade()->modifyOrder(
    'BTC-USDT',    // symbol
    0.002,         // new quantity
    orderId: '1234567890'
);

// Или по clientOrderId
$modifiedByClientId = Bingx::trade()->modifyOrder(
    'BTC-USDT',
    0.003,
    orderId: null,
    clientOrderId: 'my-order-001'
);

// Тестовый ордер (не исполняется в реальном рынке)
$testOrder = Bingx::trade()->createTestOrder(
    'BTC-USDT',           // symbol
    'BUY',                // side
    'LIMIT',              // type
    0.001,                // quantity
    positionSide: 'LONG', // positionSide
    price: 50000          // price
);

// Закрытие всех позиций по символу
$closedPositions = Bingx::trade()->closeAllPositions('BTC-USDT');

// Управление типом маржи
$marginType = Bingx::trade()->getMarginType('BTC-USDT');
Bingx::trade()->changeMarginType('BTC-USDT', 'ISOLATED'); // или 'CROSSED'

// Управление плечом
$leverage = Bingx::trade()->getLeverage('BTC-USDT');
Bingx::trade()->setLeverage('BTC-USDT', 10);
```

### 📋 Standard Contract Interface

Методы для работы со стандартными контрактами (Standard Contract Interface API):

```php
// Получить все позиции по стандартным контрактам
$positions = Bingx::contract()->getAllPositions();

// Получить историю ордеров по стандартным контрактам
$orders = Bingx::contract()->getAllOrders(
    'BTC-USDT',           // symbol
    orderId: 123456,      // начать с этого orderId (опционально)
    startTime: 1640995200000, // время начала в миллисекундах (опционально)
    endTime: 1641081600000,   // время окончания в миллисекундах (опционально)
    limit: 100            // количество результатов (опционально, макс: 1000)
);

// Получить баланс стандартного контрактного счета
$balance = Bingx::contract()->getBalance();
```

#### 📈 Открытый интерес

```php
// Открытый интерес
$openInterest = Bingx::trade()->getOpenInterest('BTC-USDT');

// История открытого интереса
$openInterestHistory = Bingx::trade()->getOpenInterestHistory('BTC-USDT', 500,
    strtotime('2024-01-01') * 1000,
    strtotime('2024-01-02') * 1000
);

// Статистика открытого интереса
$openInterestStats = Bingx::trade()->getOpenInterestStatistics('BTC-USDT', 500,
    strtotime('2024-01-01') * 1000,
    strtotime('2024-01-02') * 1000
);
```

#### ⚙️ Дополнительные настройки

```php
// Управление лимитом открытых ордеров
$maxOrders = Bingx::trade()->getMaxOpenOrders();
Bingx::trade()->setMaxOpenOrders(50);

// Плечи и номиналы
$leverageBrackets = Bingx::trade()->getNotionalAndLeverageBrackets();
$leverageForSymbol = Bingx::trade()->getLeverageBracketForSymbol('BTC-USDT');

// История доходов
$incomeHistory = Bingx::trade()->getIncomeHistory('BTC-USDT', 'COMMISSION', 100,
    strtotime('2024-01-01') * 1000,
    strtotime('2024-01-02') * 1000
);

// ADL квантиль
$adlQuantile = Bingx::trade()->getAdlQuantile('BTC-USDT');

// Принудительные ордера
$forceOrders = Bingx::trade()->getForceOrders('BTC-USDT', 100,
    strtotime('2024-01-01') * 1000,
    strtotime('2024-01-02') * 1000
);
```

#### 🧮 Расчет комиссий

```php
// Расчет комиссии фьючерсов
$commission = Bingx::trade()->calculateFuturesCommission(100, 10);
// Возвращает: ['margin' => 100, 'leverage' => 10, 'commission' => 4.5, ...]

// Пакетный расчет комиссий
$batchCommission = Bingx::trade()->calculateBatchCommission([
    ['margin' => 100, 'leverage' => 10],
    ['margin' => 200, 'leverage' => 5]
]);

// Быстрый расчет комиссии
$amount = Bingx::trade()->getCommissionAmount(100, 10);

// Ставки комиссий
$rates = Bingx::trade()->getCommissionRates();
```

### 🏗️ OrderBuilder - Продвинутое создание ордеров

OrderBuilder предоставляет удобный fluent интерфейс для создания сложных ордеров с автоматическим расчетом параметров.

#### 📋 Основные примеры

```php
// Создание фьючерсного ордера с плечом
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

// Спотовый лимитный ордер
$order = Bingx::trade()->order()
    ->spot()
    ->symbol('ETH-USDT')
    ->sell()
    ->type('LIMIT')
    ->quantity(0.1)
    ->price(3000)
    ->execute();
```

#### 🛡️ Ордера со стоп-лосс и тейк-профит

```php
// Фьючерсный лонг с защитными ордерами
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

// Фьючерсный шорт с защитой
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

#### 📊 Комплексные стратегии

```php
// Множественные ордера через OrderBuilder
$orders = [
    Bingx::trade()->order()
        ->futures()->symbol('BTC-USDT')->buy()->long()
        ->type('LIMIT')->margin(100)->price(50000)->leverage(10)
        ->stopLossPercent(5)->takeProfitPercent(15),
    
    Bingx::trade()->order()
        ->futures()->symbol('ETH-USDT')->sell()->short()
        ->type('MARKET')->margin(50)->leverage(5)
        ->stopLossPercent(3)->takeProfitPercent(10)
];

foreach ($orders as $orderBuilder) {
    $result = $orderBuilder->execute();
    echo "Order created: {$result['orderId']}\n";
}

// Динамические параметры с расчетом
$marketPrice = Bingx::market()->getLatestPrice('BTC-USDT')['price'];
$order = Bingx::trade()->order()
    ->futures()
    ->symbol('BTC-USDT')
    ->buy()
    ->long()
    ->type('LIMIT')
    ->margin(100)
    ->price($marketPrice * 0.99)  // На 1% ниже рынка
    ->leverage(10)
    ->stopLossPrice($marketPrice * 0.95)
    ->takeProfitPrice($marketPrice * 1.10)
    ->execute();
```

#### ⚙️ Расширенные параметры OrderBuilder

Ниже приведены дополнительные методы билдера, которые позволяют использовать все параметры ордеров BingX:

| Метод                               | Описание                                      | Применение          |
|-------------------------------------|-----------------------------------------------|---------------------|
| `clientOrderId('my-order-1')`       | Пользовательский ID ордера                    | Все типы            |
| `timeInForce('GTC')`                | Время жизни ордера (GTC/IOC/FOK)              | LIMIT/STOP          |
| `reduceOnly()`                      | Ордер только на сокращение позиции            | Фьючерсы            |
| `closePosition()`                   | Закрыть всю позицию                           | Фьючерсы            |
| `stopPrice(48000)`                  | Триггер-цена для STOP/TAKE_PROFIT ордеров     | Фьючерсы/условные   |
| `stopGuaranteed()`                  | Включить гарантированный стоп                 | Фьючерсы (см. доку) |
| `priceRate(0.01)`                   | Коэффициент для трейлинг/условных ордеров     | Фьючерсы            |
| `workingType('MARK_PRICE')`         | Тип цены триггера (MARK_PRICE/LAST_PRICE)     | Фьючерсы            |
| `newOrderRespType('FULL')`          | Формат ответа (ACK/RESULT/FULL)               | Все типы            |
| `positionId(123456)`                | ID позиции для привязки ордера                | Фьючерсы            |
| `timestamp($ms)`                    | Пользовательский timestamp в мс               | Все типы            |
| `recvWindow(5000)`                  | Окно валидности запроса в мс                  | Все типы            |

#### 🧬 Пример сложного фьючерсного ордера

```php
$order = Bingx::trade()->order()
    ->futures()
    ->symbol('BTC-USDT')
    ->buy()
    ->long()
    ->type('STOP_MARKET')
    ->margin(150)
    ->leverage(10)
    ->stopPrice(48000)          // Триггер для входа
    ->stopGuaranteed()          // Гарантированный стоп
    ->stopLoss(47000)           // Защитный стоп-лосс
    ->takeProfit(51000)         // Тейк-профит
    ->reduceOnly()              // Не увеличивать позицию
    ->clientOrderId('strategy-001')
    ->workingType('MARK_PRICE')
    ->newOrderRespType('FULL')
    ->recvWindow(5000)
    ->execute();
```

## 📊 Статистика библиотеки

### ✅ **Полное покрытие BingX API:**
- **MarketService**: 28 методов (рыночные данные futures + spot)
- **AccountService**: 30 методов (управление аккаунтом и активами)
- **TradeService**: 41 метод (торговые операции + OrderBuilder)
- **Всего**: 99+ методов обеспечивают **100% покрытие** официального API

### 🚀 **Ключевые возможности:**
- ✅ Все эндпоинты Market API (символы, цены, глубина, свечи, сделки)
- ✅ Полный Account API (баланс, позиции, комиссии, трансферы)
- ✅ Расширенный Trade API (ордеры, история, плечи, позиции)
- ✅ OrderBuilder для сложных торговых стратегий
- ✅ Анализ настроений рынка (лонг/шорт соотношения)
- ✅ Управление активами и dust конвертация
- ✅ Исторические данные и статистика

### 🛡️ **Безопасность и надежность:**
- ✅ Подпись запросов HMAC-SHA256
- ✅ recvWindow для защиты от replay атак
- ✅ Обработка ошибок и исключений
- ✅ Валидация параметров и типов данных
- ✅ Комплексные тесты (Unit + Integration)

### 📈 **Производительность:**
- ✅ Асинхронная поддержка HTTP клиентов
- ✅ Оптимизированные запросы и кеширование
- ✅ Пакетные операции для массовых действий
- ✅ Rate limiting и retry механизмы

## 🧪 Тестирование

Библиотека включает комплексные тесты для проверки всех эндпоинтов API.

### Установка зависимостей для тестов

```bash
composer install --dev
```

### Настройка окружения

Скопируйте файл конфигурации:

```bash
cp tests/.env.example tests/.env
```

Отредактируйте `tests/.env` и добавьте ваши API ключи:

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

#### Безопасные тесты (только чтение данных)

```bash
vendor/bin/phpunit
```

#### Все тесты (включая опасные операции)

```bash
vendor/bin/phpunit --exclude-group none
```

#### Запуск конкретных наборов тестов

```bash
# Только Unit тесты
vendor/bin/phpunit --testsuite Unit

# Только Integration тесты
vendor/bin/phpunit --testsuite Integration

# Только MarketService тесты
vendor/bin/phpunit tests/Integration/MarketServiceTest.php

# Только опасные тесты
vendor/bin/phpunit --group dangerous
```

## 🤝 Вклад в разработку

1. Fork репозиторий
2. Создайте feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit изменения (`git commit -m 'Add some AmazingFeature'`)
4. Push в branch (`git push origin feature/AmazingFeature`)
5. Откройте Pull Request

## 📄 Лицензия

MIT License - см. файл [LICENSE](LICENSE) для деталей.

## 🆘 Поддержка

- 📧 Email: support@example.com
- 💬 Telegram: [BingX PHP Community](https://t.me/bingx_php)
- 📖 Документация: [Wiki](https://github.com/username/bingx-php/wiki)
- 🐛 Issues: [GitHub Issues](https://github.com/username/bingx-php/issues)

## 🌟 Звезды

Если эта библиотека помогла вам, поставьте ⭐️ на [GitHub](https://github.com/username/bingx-php)!

---

**BingX PHP Client** - Полноценный клиент для BingX API с 100% покрытием эндпоинтов и продвинутыми возможностями торговли.
| `buy()` / `sell()`            | Направление ордера | Обязательно   |
| `type('MARKET\|LIMIT\|STOP')` | Тип ордера         | Обязательно   |
| `long()` / `short()`          | Позиция (фьючерсы) | Фьючерсы      |
| `leverage(10)`                | Плечо (1-125)      | Фьючерсы      |
| `quantity(0.001)`             | Размер ордера      | Спот          |
| `margin(100)`                 | Размер маржи       | Фьючерсы      |
| `price(50000)`                | Цена               | LIMIT/STOP    |
| `stopLoss(49000)`             | Стоп-лосс (цена)   | Фьючерсы      |
| `stopLossPercent(5)`          | Стоп-лосс (%)      | Фьючерсы      |
| `takeProfit(52000)`           | Тейк-профит (цена) | Фьючерсы      |
| `takeProfitPercent(10)`       | Тейк-профит (%)    | Фьючерсы      |
| `test()`                      | Тестовый ордер     | Все типы      |

#### 🧪 Тестовые ордера

Используйте тестовые ордера для проверки логики без реального исполнения:

```php
// Тестовый спотовый ордер
$testOrder = Bingx::trade()->order()
    ->spot()
    ->symbol('BTC-USDT')
    ->buy()
    ->type('MARKET')
    ->quantity(0.001)
    ->test()
    ->execute();

// Тестовый фьючерсный ордер со стопами
$testOrder = Bingx::trade()->order()
    ->futures()
    ->symbol('BTC-USDT')
    ->buy()
    ->long()
    ->type('LIMIT')
    ->margin(100)
    ->price(50000)
    ->stopLossPercent(5)
    ->takeProfitPercent(15)
    ->leverage(10)
    ->test()
    ->execute();
```

**Преимущества тестовых ордеров:**

- ✅ Не исполняются в реальном рынке
- ✅ Проверяют валидность параметров
- ✅ Возвращают расчетную стоимость и комиссии
- ✅ Идеальны для отладки и тестирования

#### 🎯 Продвинутые примеры

**Фьючерсный ордер с процентными стопами:**

```php
$order = Bingx::trade()->futuresOrderWithPercentages(
    'BTC-USDT',   // символ
    'BUY',        // направление
    'LONG',       // позиция
    100,          // маржа
    50000,        // цена входа
    5,            // стоп-лосс 5%
    15,           // тейк-профит 15%
    10            // плечо
);
```

**Сложный фьючерсный ордер через Builder:**

```php
$order = Bingx::trade()->order()
    ->futures()
    ->symbol('ETH-USDT')
    ->buy()
    ->long()
    ->type('LIMIT')
    ->margin(200)
    ->price(3000)
    ->stopLossPercent(3)      // стоп-лосс 3%
    ->takeProfitPercent(12)   // тейк-профит 12%
    ->leverage(20)
    ->execute();
```

**Валидация и ошибки:**

```php
try {
    $order = Bingx::trade()->order()
        ->futures()
        ->symbol('BTC-USDT')
        ->buy()
        ->type('LIMIT')
        ->price(50000)
        ->execute(); // Ошибка: нет маржи или позиции
} catch (\InvalidArgumentException $e) {
    echo "Ошибка валидации: " . $e->getMessage();
}
```

#### 📊 Стандартные методы торговли

```php
// Создание ордера (базовый метод)
$order = Bingx::trade()->createOrder([
    'symbol' => 'BTC-USDT',
    'side' => 'BUY',
    'type' => 'MARKET',
    'quantity' => 0.001,
    'positionSide' => 'LONG'
]);

// Пакетное создание ордеров
$batchOrders = Bingx::trade()->createBatchOrders([
    [
        'symbol' => 'BTC-USDT',
        'side' => 'BUY',
        'type' => 'LIMIT',
        'quantity' => 0.001,
        'price' => 50000
    ]
]);

// Отмена ордера
Bingx::trade()->cancelOrder('BTC-USDT', '123456789');

// Отмена всех ордеров
Bingx::trade()->cancelAllOrders('BTC-USDT');

// Пакетная отмена ордеров
Bingx::trade()->cancelBatchOrders('BTC-USDT', ['123456789', '987654321']);

// Получение информации об ордере
$orderInfo = Bingx::trade()->getOrder('BTC-USDT', '123456789');

// Открытые ордера
$openOrders = Bingx::trade()->getOpenOrders();

// История ордеров
$orderHistory = Bingx::trade()->getOrderHistory('BTC-USDT', 100);

// История сделок
$trades = Bingx::trade()->getUserTrades('BTC-USDT');
```

#### ❌ Отмена ордеров - Примеры использования

```php
// Отменить конкретный ордер
$result = Bingx::trade()->cancelOrder('BTC-USDT', '123456789');

// Отменить все открытые ордера для символа
$result = Bingx::trade()->cancelAllOrders('BTC-USDT');

// Отменить несколько ордеров сразу
$orderIds = ['123456789', '987654321', '555666777'];
$result = Bingx::trade()->cancelBatchOrders('BTC-USDT', $orderIds);

// Получить открытые ордера и отменить их по условию
$openOrders = Bingx::trade()->getOpenOrders('BTC-USDT');
$buyOrderIds = [];

foreach ($openOrders['orders'] as $order) {
    if ($order['side'] === 'BUY') {
        $buyOrderIds[] = $order['orderId'];
    }
}

if (!empty($buyOrderIds)) {
    $result = Bingx::trade()->cancelBatchOrders('BTC-USDT', $buyOrderIds);
    echo "Отменено " . count($buyOrderIds) . " ордеров на покупку\n";
}

// Проверка результата отмены
if ($result['code'] === 0) {
    echo "Ордер успешно отменен!";
} else {
    echo "Ошибка отмены: " . $result['msg'];
}
```

#### 💰 Расчёт торговой комиссии

```php
// Расчёт комиссии для фьючерсной сделки
$commission = Bingx::trade()->calculateFuturesCommission(100, 10);
// Возвращает:
// [
//     'margin' => 100,
//     'leverage' => 10,
//     'position_value' => 1000,
//     'commission_rate' => 0.00045,
//     'commission_rate_percent' => 0.045,
//     'commission' => 0.45,
//     'commission_rounded' => 0.45,
//     'net_position_value' => 999.55
// ]

// Быстрый расчёт суммы комиссии
$commissionAmount = Bingx::trade()->getCommissionAmount(100, 10); // 0.45

// Пакетный расчёт комиссии для нескольких ордеров
$orders = [
    ['margin' => 100, 'leverage' => 10],
    ['margin' => 200, 'leverage' => 5],
    ['margin' => 150, 'leverage' => 20]
];
$batchCommission = Bingx::trade()->calculateBatchCommission($orders);

// Получение информации о ставках комиссии
$rates = Bingx::trade()->getCommissionRates();
// Возвращает ставку 0.045% и формулу расчёта

// Расчёт с кастомной ставкой комиссии
$customCommission = Bingx::trade()->calculateFuturesCommission(
    100, 
    10, 
    0.0005 // 0.05% кастомная ставка
);
```

**Формула расчёта комиссии BingX:**

```
Комиссия = Маржа × Плечо × 0.045%
```

**Примеры расчёта:**

```php
// BTC лонг с плечом 10x, маржа 100 USDT
$commission = Bingx::trade()->calculateFuturesCommission(100, 10);
echo "Комиссия: {$commission['commission']} USDT\n";
echo "Чистая стоимость позиции: {$commission['net_position_value']} USDT\n";

// ETH шорт с плечом 5x, маржа 200 USDT  
$ethCommission = Bingx::trade()->calculateFuturesCommission(200, 5);
echo "Комиссия ETH: {$ethCommission['commission']} USDT\n";

// Расчёт для портфеля из 5 сделок
$portfolio = [
    ['margin' => 100, 'leverage' => 10],
    ['margin' => 150, 'leverage' => 8],
    ['margin' => 200, 'leverage' => 5],
    ['margin' => 120, 'leverage' => 15],
    ['margin' => 80, 'leverage' => 20]
];
$totalCommission = Bingx::trade()->calculateBatchCommission($portfolio);
echo "Общая комиссия портфеля: {$totalCommission['total_commission']} USDT\n";
```

### 🔄 Обратная совместимость

Старые методы продолжают работать для совместимости:

```php
// Legacy методы (все еще работают)
$balance = Bingx::getBalance();
$symbols = Bingx::getSymbols();
$order = Bingx::createOrder([...]);
```

### 🎯 Dependency Injection

```php
use Tigusigalpa\BingX\BingxClient;

class TradingController
{
    public function __construct(BingxClient $bingx)
    {
        $this->bingx = $bingx;
    }

    public function getBalance()
    {
        return $this->bingx->account()->getBalance();
    }
}
```

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
    // Ошибка аутентификации
    echo "Auth error: " . $e->getMessage();
} catch (RateLimitException $e) {
    // Превышен лимит запросов
    echo "Rate limit exceeded";
} catch (InsufficientBalanceException $e) {
    // Недостаточно средств
    echo "Insufficient balance";
} catch (ApiException $e) {
    // Другие ошибки API
    echo "API error: " . $e->getErrorCode();
} catch (BingxException $e) {
    // Общие ошибки
    echo "BingX error: " . $e->getMessage();
}
```

## 🧪 Тестирование

Библиотека включает комплексные тесты для проверки всех эндпоинтов API.

### Установка зависимостей для тестов

```bash
composer install --dev
```

### Настройка окружения

Скопируйте файл конфигурации:

```bash
cp tests/.env.example tests/.env
```

Отредактируйте `tests/.env` и добавьте ваши API ключи:

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

#### Безопасные тесты (только чтение данных)

```bash
vendor/bin/phpunit
```

#### Все тесты (включая опасные операции)

```bash
vendor/bin/phpunit --group dangerous
```

#### Отдельные сервисы

```bash
# Market Service тесты
vendor/bin/phpunit tests/Integration/MarketServiceTest.php

# Account Service тесты  
vendor/bin/phpunit tests/Integration/AccountServiceTest.php

# Trade Service тесты
vendor/bin/phpunit tests/Integration/TradeServiceTest.php
```

#### Unit тесты

```bash
vendor/bin/phpunit tests/Unit/
```

### Структура тестов

- **Unit тесты** (`tests/Unit/`) - проверяют базовую функциональность без API вызовов
- **Integration тесты** (`tests/Integration/`) - проверяют реальные эндпоинты BingX API
- **Безопасные тесты** (`@group safe`) - только чтение данных (рыночная информация, баланс, история)
- **Опасные тесты** (`@group dangerous`) - операции, изменяющие данные (ордера, плечо, маржа)

### Примеры тестов

#### Market Service тесты

```php
public function testGetAllSymbols(): void
{
    $response = $this->client->market()->getAllSymbols();
    
    $this->assertSuccessResponse($response);
    $this->assertArrayHasKey('spot', $response);
    $this->assertArrayHasKey('futures', $response);
}

public function testGetLatestPrice(): void
{
    $symbol = 'BTC-USDT';
    $response = $this->client->market()->getLatestPrice($symbol);
    
    $this->assertSuccessResponse($response);
    $this->assertArrayHasKey('price', $response);
    $this->assertIsNumeric($response['price']);
}
```

#### Account Service тесты

```php
public function testGetBalance(): void
{
    $response = $this->client->account()->getBalance();
    
    $this->assertSuccessResponse($response);
    $this->assertArrayHasKey('balance', $response);
}

public function testGetLeverage(): void
{
    $symbol = 'BTC-USDT';
    $response = $this->client->account()->getLeverage($symbol);
    
    $this->assertSuccessResponse($response);
    $this->assertArrayHasKey('leverage', $response);
}
```

#### Trade Service тесты

```php
public function testCreateTestOrder(): void
{
    $response = $this->client->trade()->createTestOrder([
        'symbol' => 'BTC-USDT',
        'side' => 'BUY',
        'type' => 'MARKET',
        'quantity' => 0.001
    ]);
    
    $this->assertSuccessResponse($response);
    $this->assertArrayHasKey('orderId', $response);
}

public function testCalculateFuturesCommission(): void
{
    $commission = $this->client->trade()->getCommissionAmount(100, 10);
    
    $this->assertEquals(0.45, $commission);
}
```

### Покрытие тестами

Тесты покрывают все основные функции библиотеки:

- ✅ **Market Service**: все эндпоинты рыночных данных
- ✅ **Account Service**: баланс, позиции, плечо, маржа
- ✅ **Trade Service**: ордера, история, комиссии, тестовые операции
- ✅ **OrderBuilder**: построение ордеров всех типов
- ✅ **Обработка ошибок**: валидация ответов API

### Рекомендации по запуску

1. **Для разработки**: запускайте только безопасные тесты
2. **Для регрессии**: запускайте все тесты на тестовом аккаунте
3. **Перед релизом**: полный прогон всех тестов
4. **CI/CD**: используйте безопасные тесты для автоматической проверки

## 📖 Документация API

- **Авторизация**: https://bingx-api.github.io/docs/#/en-us/swapV2/authentication.html
- **Ошибки**: https://bingx-api.github.io/docs/#/en-us/swapV2/base-info.html
- **Market Data**: https://bingx-api.github.io/docs/#/en-us/swapV2/market-api.html
- **Account Data**: https://bingx-api.github.io/docs/#/en-us/swapV2/account-api.html
- **Trade Endpoints**: https://bingx-api.github.io/docs/#/en-us/swapV2/trade-api.html

## 🏷️ Версии

- **2.0.0** - Полный рефакторинг: модульная архитектура, обработка ошибок, все эндпоинты API
- **1.0.0** - Базовая авторизация и простые обертки
- **0.1.0** - Первоначальная версия

## 👨‍💻 Автор и лицензия

- **Автор**: Igor Sazonov (`tigusigalpa`)
- **Email**: `sovletig@gmail.com`
- **GitHub**: https://github.com/tigusigalpa/bingx-php
- **Лицензия**: MIT

## 🤝 Вклад

Pull requests приветствуются! Пожалуйста, убедитесь, что:

- Код соответствует PSR-12
- Добавлены тесты для новой функциональности
- Обновлена документация

## 📄 Лицензия

MIT License - см. файл LICENSE для деталей.
