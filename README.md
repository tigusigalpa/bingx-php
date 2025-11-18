# BingX PHP — клиент Swap V2 API с интеграцией Laravel 8–12

Профессиональная библиотека для работы с BingX Swap V2 API с полной поддержкой всех эндпоинтов: маркет-данные, управление аккаунтом, торговые операции. Включает модульную архитектуру, продвинутую обработку ошибок и полную интеграцию с Laravel 8–12.

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

// Ставка финансирования (только фьючерсы)
$funding = Bingx::market()->getFundingRateHistory('BTC-USDT');

// Марковая цена (только фьючерсы)
$markPrice = Bingx::market()->getMarkPrice('BTC-USDT');
```

#### 🔍 Примеры использования

```php
// Получить все активные BTC пары
$allSymbols = Bingx::market()->getAllSymbols();
$spotPairs = array_filter($allSymbols['spot'], function($symbol) {
    return strpos($symbol['symbol'], 'BTC') === 0;
});
$futuresPairs = array_filter($allSymbols['futures'], function($symbol) {
    return strpos($symbol['symbol'], 'BTC') === 0;
});

// Получить цены для всех BTC пар
foreach ($spotPairs as $pair) {
    $price = Bingx::market()->getSpotLatestPrice($pair['symbol']);
    echo "{$pair['symbol']}: {$price['price']}\n";
}

// Сравнить спотовую и фьючерсную цены
$spotPrice = Bingx::market()->getSpotLatestPrice('BTC-USDT')['price'];
$futuresPrice = Bingx::market()->getLatestPrice('BTC-USDT')['price'];
$spread = $futuresPrice - $spotPrice;
echo "Spread: {$spread} USDT\n";
```

### 👤 Account Service - Управление аккаунтом

```php
// Баланс аккаунта
$balance = Bingx::account()->getBalance();

// Открытые позиции
$positions = Bingx::account()->getPositions();

// Информация об аккаунте
$account = Bingx::account()->getAccountInfo();

// Торговые комиссии
$fees = Bingx::account()->getTradingFees('BTC-USDT');

// Управление кредитным плечом (получение и установка)
// Получить текущее плечо по символу
$leverageInfo = Bingx::account()->getLeverage('BTC-USDT');

// Установить плечо для разных сторон
Bingx::account()->setLeverage('BTC-USDT', 'LONG', 10);   // LONG плечо 10x
Bingx::account()->setLeverage('BTC-USDT', 'SHORT', 5);   // SHORT плечо 5x
Bingx::account()->setLeverage('BTC-USDT', 'BOTH', 20);   // one-way режим, плечо 20x

// Управление маржой
Bingx::account()->setMarginMode('BTC-USDT', 'ISOLATED');
```

#### ⚙️ Управление кредитным плечом

```php
// Получить плечо
$leverage = Bingx::account()->getLeverage('BTC-USDT');

// Получить плечо с указанием окна валидности запроса (recvWindow)
$leverage = Bingx::account()->getLeverage('BTC-USDT', 5000); // 5 секунд

// Установить плечо для LONG/SHORT/BOTH
Bingx::account()->setLeverage('BTC-USDT', 'LONG', 10);      // Лонг 10x
Bingx::account()->setLeverage('BTC-USDT', 'SHORT', 5);      // Шорт 5x
Bingx::account()->setLeverage('BTC-USDT', 'BOTH', 20);      // One-way режим 20x

// То же через TradeService
Bingx::trade()->changeLeverage('BTC-USDT', 'BOTH', 15);
```

### 💱 Trade Service - Торговые операции

#### 🚀 Быстрые методы для спотовой торговли

```php
// Рыночная покупка
$order = Bingx::trade()->spotMarketBuy('BTC-USDT', 0.001);

// Рыночная продажа
$order = Bingx::trade()->spotMarketSell('BTC-USDT', 0.001);

// Лимитная покупка
$order = Bingx::trade()->spotLimitBuy('BTC-USDT', 0.001, 50000);

// Лимитная продажа
$order = Bingx::trade()->spotLimitSell('BTC-USDT', 0.001, 50000);
```

#### 🎯 Быстрые методы для фьючерсной торговли

```php
// Лонг рыночный ордер
$order = Bingx::trade()->futuresLongMarket('BTC-USDT', 100, 10);

// Шорт рыночный ордер
$order = Bingx::trade()->futuresShortMarket('BTC-USDT', 100, 10);

// Лонг лимитный со стоп-лосс и тейк-профит
$order = Bingx::trade()->futuresLongLimit(
    'BTC-USDT',  // символ
    100,         // маржа
    50000,       // цена входа
    49000,       // стоп-лосс
    52000,       // тейк-профит
    10           // плечо
);

// Шорт лимитный со стоп-лосс и тейк-профит
$order = Bingx::trade()->futuresShortLimit(
    'BTC-USDT', 100, 50000, 51000, 48000, 10
);
```

#### 🏗️ OrderBuilder - Продвинутое создание ордеров

Используйте fluent API для максимальной гибкости:

```php
// Базовый пример
$order = Bingx::trade()->order()
    ->spot()
    ->symbol('BTC-USDT')
    ->buy()
    ->type('MARKET')
    ->quantity(0.001)
    ->execute();
```

#### 📋 Параметры OrderBuilder

| Метод | Описание | Применение |
|-------|----------|------------|
| `spot()` / `futures()` | Тип торгов | Спот/Фьючерсы |
| `symbol('BTC-USDT')` | Торговый символ | Обязательно |
| `buy()` / `sell()` | Направление ордера | Обязательно |
| `type('MARKET\|LIMIT\|STOP')` | Тип ордера | Обязательно |
| `long()` / `short()` | Позиция (фьючерсы) | Фьючерсы |
| `leverage(10)` | Плечо (1-125) | Фьючерсы |
| `quantity(0.001)` | Размер ордера | Спот |
| `margin(100)` | Размер маржи | Фьючерсы |
| `price(50000)` | Цена | LIMIT/STOP |
| `stopLoss(49000)` | Стоп-лосс (цена) | Фьючерсы |
| `stopLossPercent(5)` | Стоп-лосс (%) | Фьючерсы |
| `takeProfit(52000)` | Тейк-профит (цена) | Фьючерсы |
| `takeProfitPercent(10)` | Тейк-профит (%) | Фьючерсы |
| `test()` | Тестовый ордер | Все типы |

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