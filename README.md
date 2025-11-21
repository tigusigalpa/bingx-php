# BingX PHP SDK

<div align="center">
  <img src="https://github.com/user-attachments/assets/bc9acf4c-79c7-4e02-bb8d-75f2d8784b29" alt="BingX PHP SDK" style="max-width: 100%; height: auto;">
  
  [![PHP Version](https://img.shields.io/badge/php-%3E%3D8.1-blue?style=flat-square&logo=php)](https://www.php.net/)
  [![Composer](https://img.shields.io/badge/composer-v2-orange?style=flat-square&logo=composer)](https://getcomposer.org/)
  [![License](https://img.shields.io/badge/license-MIT-green?style=flat-square)](LICENSE)
  [![GitHub Stars](https://img.shields.io/github/stars/tigusigalpa/bingx-php?style=flat-square&logo=github)](https://github.com/tigusigalpa/bingx-php)
  [![Latest Release](https://img.shields.io/github/v/release/tigusigalpa/bingx-php?style=flat-square&logo=github)](https://github.com/tigusigalpa/bingx-php/releases)
  [![Test Coverage](https://img.shields.io/badge/coverage-119%2B%20tests-brightgreen?style=flat-square)](#-testing)
  
  **Full-Featured PHP SDK for BingX Swap V2 API**
  
  Cryptocurrency Trading | Market Data | WebSocket Streams | Laravel Integration
  
  [English](./README.md) | [Русский](./README-ru.md)
</div>

---

## 📖 Table of Contents

- [About](#-about)
- [Features](#-features)
- [Quick Start](#-quick-start)
- [Installation](#-installation)
- [Usage](#-usage)
  - [Market Service](#-market-service---market-data)
  - [Account Service](#-account-service---account-management)
  - [Trade Service](#-trade-service---trading-operations)
  - [WebSocket API](#-websocket-api)
- [OrderBuilder](#-orderbuilder---advanced-order-creation)
- [Error Handling](#-error-handling)
- [Testing](#-testing)
- [Documentation](#-documentation)
- [License](#-license)

---

## ✨ About

**BingX PHP SDK** is a professional, feature-rich library for working with the BingX Swap V2 API.

Built with modern PHP practices and provides:
- ✅ **100% coverage** of all BingX API endpoints
- ✅ **Modular architecture** with separate services
- ✅ **Laravel 8-12 integration** with auto-discovery
- ✅ **Advanced error handling** with custom exceptions
- ✅ **WebSocket support** for streaming data
- ✅ **Complete security** with HMAC-SHA256 signatures
- ✅ **119+ methods** for full trading control

---

## 🚀 Features

### 📊 Supported Services

| Service | Description | Methods |
|---------|-------------|---------|
| 🏪 **Market Service** | Market data, symbols, prices, candles, trades | 28 |
| 👤 **Account Service** | Balance, positions, leverage, margin, fees | 30 |
| 🔄 **Trade Service** | Orders, trade history, position management | 41 |
| 💰 **Wallet Service** | Deposits, withdrawals, wallet addresses | 6 |
| 💵 **Spot Account Service** | Spot balance, transfers, internal transfers | 8 |
| 📋 **Contract Service** | Standard contract API | 3 |
| 🔐 **Listen Key Service** | WebSocket authentication | 3 |

### 🛡️ Security

- ✅ HMAC-SHA256 signature for all requests
- ✅ Automatic timestamp validation
- ✅ Support for base64 and hex signature encoding
- ✅ recvWindow for replay attack protection
- ✅ Custom exceptions for different error types

### 🔧 Developer Experience

- ✅ Fluent interface for order building
- ✅ IDE autocomplete with type hints
- ✅ Comprehensive error messages
- ✅ Full test coverage with examples
- ✅ Support for pure PHP and Laravel

---

## ⚡ Quick Start

### With Laravel

```php
// Get current price
$price = Bingx::market()->getLatestPrice('BTC-USDT');
echo "BTC price: {$price['price']}";

// Get account balance
$balance = Bingx::account()->getBalance();

// Create an order using OrderBuilder
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

### Pure PHP

```php
use Tigusigalpa\BingX\BingxClient;
use Tigusigalpa\BingX\Http\BaseHttpClient;

$http = new BaseHttpClient('API_KEY', 'API_SECRET', 'https://open-api.bingx.com');
$bingx = new BingxClient($http);

$price = $bingx->market()->getLatestPrice('BTC-USDT');
```

---

## 📦 Installation

### Requirements

- PHP >= 8.1
- Composer
- (Optional) Laravel 8-12 for integration

### Step 1: Add Repository

In your root `composer.json`:

```json
{
  "repositories": [
    { "type": "path", "url": "packages/bingx-php" }
  ]
}
```

### Step 2: Install Package

```bash
composer require tigusigalpa/bingx-php:*
```

### Step 3: (Laravel Only) Publish Configuration

```bash
php artisan vendor:publish --tag=bingx-config
```

### Step 4: Configure Environment Variables

Add to `.env`:

```env
BINGX_API_KEY=your_api_key_here
BINGX_API_SECRET=your_api_secret_here
BINGX_SOURCE_KEY=optional_source_key
BINGX_BASE_URI=https://open-api.bingx.com
BINGX_SIGNATURE_ENCODING=base64
```

### 🔑 Creating API Keys

1. Go to [BingX API Settings](https://bingx.com/en-US/accounts/api)
2. Click "Create API"
3. Save your **API Key** and **Secret Key** in a secure location
4. Configure access rights
5. ⚠️ Secret Key is displayed only once!

---

## 📚 Usage

### 🏪 Market Service - Market Data

#### Trading Pairs and Symbols

```php
// Get all available symbols (spot + futures)
$allSymbols = Bingx::market()->getAllSymbols();
// ['spot' => [...], 'futures' => [...]]

// Only spot pairs
$spotSymbols = Bingx::market()->getSpotSymbols();

// Only futures symbols
$futuresSymbols = Bingx::market()->getFuturesSymbols();
```

#### Prices and Statistics

```php
// Current price
$futuresPrice = Bingx::market()->getLatestPrice('BTC-USDT');
$spotPrice = Bingx::market()->getSpotLatestPrice('BTC-USDT');

// 24h statistics
$ticker = Bingx::market()->get24hrTicker('BTC-USDT');
$spotTicker = Bingx::market()->getSpot24hrTicker('BTC-USDT');

// All symbols at once
$allTickers = Bingx::market()->get24hrTicker();
```

#### Market Depth and Candles

```php
// Order book depth
$depth = Bingx::market()->getDepth('BTC-USDT', 20);
$spotDepth = Bingx::market()->getSpotDepth('BTC-USDT', 20);

// Candlesticks
$klines = Bingx::market()->getKlines('BTC-USDT', '1h', 100);
$spotKlines = Bingx::market()->getSpotKlines('BTC-USDT', '1h', 100);

// With time range
$klines = Bingx::market()->getKlines(
    'BTC-USDT', '1h', 100,
    strtotime('2024-01-01') * 1000,
    strtotime('2024-01-02') * 1000
);
```

#### Funding and Mark Price

```php
// Funding rate history
$fundingRate = Bingx::market()->getFundingRateHistory('BTC-USDT', 100);

// Mark price
$markPrice = Bingx::market()->getMarkPrice('BTC-USDT');

// Premium index
$premiumKlines = Bingx::market()->getPremiumIndexKlines('BTC-USDT', '1h', 100);

// Continuous contracts
$continuousKlines = Bingx::market()->getContinuousKlines('BTC-USDT', '1h', 100);
```

#### Trades

```php
// Aggregate trades
$aggTrades = Bingx::market()->getAggregateTrades('BTC-USDT', 500);

// Recent trades
$recentTrades = Bingx::market()->getRecentTrades('BTC-USDT', 500);

// Spot trades
$spotAggTrades = Bingx::market()->getSpotAggregateTrades('BTC-USDT', 500);
$spotRecentTrades = Bingx::market()->getSpotRecentTrades('BTC-USDT', 500);
```

#### Market Sentiment Analysis

```php
// Long/Short ratio
$longShortRatio = Bingx::market()->getTopLongShortRatio('BTC-USDT', 10);

// Historical ratios
$historicalRatio = Bingx::market()->getHistoricalTopLongShortRatio(
    'BTC-USDT', 500,
    strtotime('2024-01-01') * 1000,
    strtotime('2024-01-02') * 1000
);

// Contract basis data
$basis = Bingx::market()->getBasis('BTC-USDT', 'PERPETUAL', 100);
```

---

### 👤 Account Service - Account Management

#### Balance and Positions

```php
// Get account balance
$balance = Bingx::account()->getBalance();

// All positions
$allPositions = Bingx::account()->getPositions();

// Positions for specific symbol
$positions = Bingx::account()->getPositions('BTC-USDT');

// Account information
$accountInfo = Bingx::account()->getAccountInfo();
```

#### Leverage and Margin Management

```php
// Get current leverage
$leverage = Bingx::account()->getLeverage('BTC-USDT');

// Set leverage
Bingx::account()->setLeverage('BTC-USDT', 'BOTH', 20);

// Margin mode (ISOLATED or CROSSED)
$marginMode = Bingx::account()->getMarginMode('BTC-USDT');
Bingx::account()->setMarginMode('BTC-USDT', 'ISOLATED');

// Position margin
Bingx::account()->setPositionMargin('BTC-USDT', 'LONG', 100.0, 1);
```

#### Trading Fees

```php
// Fees for symbol
$fees = Bingx::account()->getTradingFees('BTC-USDT');

// API permissions
$permissions = Bingx::account()->getAccountPermissions();

// API key information
$apiKey = Bingx::account()->getApiKey();

// User commission rates
$userRates = Bingx::account()->getUserCommissionRates('BTC-USDT');

// API rate limits
$rateLimits = Bingx::account()->getApiRateLimits();
```

#### Operation History

```php
// Balance history
$balanceHistory = Bingx::account()->getBalanceHistory('USDT', 100);

// Deposit history
$deposits = Bingx::account()->getDepositHistory('USDT', 100);

// Withdrawal history
$withdrawals = Bingx::account()->getWithdrawHistory('USDT', 100);
```

#### Asset Management

```php
// Asset details
$assetDetails = Bingx::account()->getAssetDetails('USDT');

// All available assets
$allAssets = Bingx::account()->getAllAssets();

// Funding wallet
$fundingWallet = Bingx::account()->getFundingWallet('USDT');

// Dust conversion (small assets)
Bingx::account()->dustTransfer(['BTC', 'ETH']);
```

---

### 🔄 Trade Service - Trading Operations

#### Quick Trade Methods

```php
// Spot orders
$buy = Bingx::trade()->spotMarketBuy('BTC-USDT', 0.001);
$sell = Bingx::trade()->spotMarketSell('BTC-USDT', 0.001);

// Spot limit orders
$limitBuy = Bingx::trade()->spotLimitBuy('BTC-USDT', 0.001, 50000);
$limitSell = Bingx::trade()->spotLimitSell('BTC-USDT', 0.001, 60000);

// Futures orders
$longOrder = Bingx::trade()->futuresLongMarket('BTC-USDT', 100, 10);
$shortOrder = Bingx::trade()->futuresShortMarket('BTC-USDT', 100, 10);

// With protective orders
$longLimit = Bingx::trade()->futuresLongLimit(
    'BTC-USDT', 100, 50000, 48000, 55000, 10
);
```

#### Creating and Managing Orders

```php
// Create order
$order = Bingx::trade()->createOrder([
    'symbol' => 'BTC-USDT',
    'side' => 'BUY',
    'type' => 'MARKET',
    'quantity' => 0.001
]);

// Test order (no execution)
$testOrder = Bingx::trade()->createTestOrder([
    'symbol' => 'BTC-USDT',
    'side' => 'BUY',
    'type' => 'LIMIT',
    'quantity' => 0.001,
    'price' => 50000
]);

// Batch creation
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

#### Canceling Orders

```php
// Cancel specific order
Bingx::trade()->cancelOrder('BTC-USDT', '123456789');

// Cancel all orders for symbol
Bingx::trade()->cancelAllOrders('BTC-USDT');

// Batch cancel
Bingx::trade()->cancelBatchOrders('BTC-USDT', ['123456789', '987654321']);

// Cancel and replace
Bingx::trade()->cancelAndReplaceOrder(
    'BTC-USDT', '123456789', 'BUY', 'LIMIT', 0.001, 50000
);
```

#### Getting Order Information

```php
// Order details
$order = Bingx::trade()->getOrder('BTC-USDT', '123456789');

// Open orders
$openOrders = Bingx::trade()->getOpenOrders();
$openOrdersForSymbol = Bingx::trade()->getOpenOrders('BTC-USDT', 50);

// Order history
$orderHistory = Bingx::trade()->getOrderHistory('BTC-USDT', 100);

// Trade history
$userTrades = Bingx::trade()->getUserTrades('BTC-USDT', 100);
```

#### Fee Calculation

```php
// Calculate futures fee
$commission = Bingx::trade()->calculateFuturesCommission(100, 10);
// Returns detailed fee information

// Quick amount calculation
$amount = Bingx::trade()->getCommissionAmount(100, 10); // 0.45

// Batch calculation
$batchCommission = Bingx::trade()->calculateBatchCommission([
    ['margin' => 100, 'leverage' => 10],
    ['margin' => 200, 'leverage' => 5]
]);

// Get fee rates
$rates = Bingx::trade()->getCommissionRates();
```

#### Position Management

```php
// Position mode
$positionMode = Bingx::trade()->getPositionMode();
Bingx::trade()->setPositionMode('HEDGE_MODE');

// Position side
$positionSide = Bingx::trade()->getPositionSide();
Bingx::trade()->setPositionSide('BOTH');

// Close all positions
Bingx::trade()->closeAllPositions('BTC-USDT');

// Margin type
$marginType = Bingx::trade()->getMarginType('BTC-USDT');
Bingx::trade()->changeMarginType('BTC-USDT', 'ISOLATED');
```

---

### 💰 Wallet Service - Wallet Management

```php
// Deposit history
$deposits = Bingx::wallet()->getDepositHistory(
    coin: 'USDT',
    status: 1,
    startTime: strtotime('2024-01-01') * 1000,
    endTime: strtotime('2024-01-31') * 1000
);

// Deposit address
$address = Bingx::wallet()->getDepositAddress('USDT', 'TRC20');

// Withdrawal history
$withdrawals = Bingx::wallet()->getWithdrawalHistory(
    coin: 'USDT',
    status: 6
);

// Create withdrawal
$withdrawal = Bingx::wallet()->withdraw(
    coin: 'USDT',
    address: 'TXxx...xxx',
    amount: 100.0,
    network: 'TRC20'
);

// Coin information
$coins = Bingx::wallet()->getAllCoinInfo();
```

---

### 💵 Spot Account Service - Spot Account

```php
// Spot account balance
$balance = Bingx::spotAccount()->getBalance();

// Fund balance
$fundBalance = Bingx::spotAccount()->getFundBalance();

// Universal transfer
$transfer = Bingx::spotAccount()->universalTransfer(
    type: 'FUND_PFUTURES',
    asset: 'USDT',
    amount: 100.0
);

// Transfer history
$history = Bingx::spotAccount()->getAssetTransferRecords(
    type: 'FUND_PFUTURES',
    startTime: strtotime('2024-01-01') * 1000,
    endTime: strtotime('2024-01-31') * 1000
);

// Internal transfer (main -> sub account)
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

#### Install Dependency

```bash
composer require textalk/websocket
```

#### Market Data Stream (Public Data)

```php
use Tigusigalpa\BingX\WebSocket\MarketDataStream;

$stream = new MarketDataStream();
$stream->connect();

// Subscribe to events
$stream->subscribeTrade('BTC-USDT');
$stream->subscribeKline('BTC-USDT', '1m');
$stream->subscribeDepth('BTC-USDT', 20);
$stream->subscribeTicker('BTC-USDT');
$stream->subscribeBookTicker('BTC-USDT');

// Handle messages
$stream->onMessage(function ($data) {
    echo "Data: " . json_encode($data) . PHP_EOL;
    
    if (isset($data['dataType'])) {
        switch ($data['dataType']) {
            case 'BTC-USDT@trade':
                echo "New trade: {$data['data']['p']}";
                break;
            case 'BTC-USDT@kline_1m':
                echo "New candle";
                break;
        }
    }
});

// Start listening
$stream->listen();

// Unsubscribe and close
$stream->unsubscribeTrade('BTC-USDT');
$stream->disconnect();
```

#### Account Data Stream (Private Data)

```php
use Tigusigalpa\BingX\WebSocket\AccountDataStream;

// 1. Get Listen Key
$response = Bingx::listenKey()->generate();
$listenKey = $response['listenKey'];

// 2. Create connection
$stream = new AccountDataStream($listenKey);
$stream->connect();

// 3. Listen for updates
$stream->onBalanceUpdate(function ($balances) {
    foreach ($balances as $balance) {
        echo "Balance {$balance['a']}: {$balance['wb']}";
    }
});

$stream->onPositionUpdate(function ($positions) {
    foreach ($positions as $position) {
        echo "Position {$position['s']}: {$position['pa']}";
    }
});

$stream->onOrderUpdate(function ($order) {
    echo "Order #{$order['i']}: {$order['X']}";
});

// 4. Start listening
$stream->listen();

// 5. Extend Listen Key (every 30 minutes)
Bingx::listenKey()->extend($listenKey);

// 6. Close connection
Bingx::listenKey()->delete($listenKey);
$stream->disconnect();
```

#### Listen Key Management

```php
// Create new key (valid for 60 minutes)
$response = Bingx::listenKey()->generate();
$listenKey = $response['listenKey'];

// Extend validity (recommended every 30 minutes)
Bingx::listenKey()->extend($listenKey);

// Delete key
Bingx::listenKey()->delete($listenKey);
```

---

## 🎯 OrderBuilder - Advanced Order Creation

OrderBuilder provides a convenient fluent interface for creating complex orders with automatic calculation.

### Simple Examples

```php
// Futures order with leverage
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

// Spot limit order
$order = Bingx::trade()->order()
    ->spot()
    ->symbol('ETH-USDT')
    ->sell()
    ->type('LIMIT')
    ->quantity(0.1)
    ->price(3000)
    ->execute();
```

### Orders with Protective Stops

```php
// Long with percentage stops
$order = Bingx::trade()->order()
    ->futures()
    ->symbol('BTC-USDT')
    ->buy()
    ->long()
    ->type('LIMIT')
    ->margin(200)
    ->price(50000)
    ->leverage(10)
    ->stopLossPercent(5)      // Stop Loss 5% below
    ->takeProfitPercent(15)   // Take Profit 15% above
    ->execute();

// Short with fixed prices
$order = Bingx::trade()->order()
    ->futures()
    ->symbol('BTC-USDT')
    ->sell()
    ->short()
    ->type('MARKET')
    ->margin(150)
    ->leverage(5)
    ->stopLossPrice(52000)    // Fixed Stop Loss
    ->takeProfitPrice(45000)  // Fixed Take Profit
    ->execute();
```

### Advanced Parameters

```php
$order = Bingx::trade()->order()
    ->futures()
    ->symbol('BTC-USDT')
    ->buy()
    ->long()
    ->type('STOP_MARKET')
    ->margin(150)
    ->leverage(10)
    ->stopPrice(48000)              // Entry trigger
    ->stopGuaranteed()              // Guaranteed stop
    ->stopLoss(47000)               // Protective stop loss
    ->takeProfit(51000)             // Take profit
    ->reduceOnly()                  // Don't increase position
    ->clientOrderId('strategy-001')
    ->workingType('MARK_PRICE')
    ->newOrderRespType('FULL')
    ->recvWindow(5000)
    ->execute();
```

### Available OrderBuilder Methods

| Method | Description | Use Case |
|--------|-------------|----------|
| `spot()` / `futures()` | Market type | Required |
| `symbol('BTC-USDT')` | Trading pair | Required |
| `buy()` / `sell()` | Direction | Required |
| `type('MARKET\|LIMIT\|STOP')` | Order type | Required |
| `long()` / `short()` | Position | Futures |
| `leverage(10)` | Leverage (1-125) | Futures |
| `quantity(0.001)` | Order size | Spot |
| `margin(100)` | Margin | Futures |
| `price(50000)` | Price | LIMIT/STOP |
| `stopLoss(49000)` | Stop loss (price) | Futures |
| `stopLossPercent(5)` | Stop loss (%) | Futures |
| `takeProfit(52000)` | Take profit (price) | Futures |
| `takeProfitPercent(10)` | Take profit (%) | Futures |
| `clientOrderId('id')` | Custom ID | All types |
| `timeInForce('GTC')` | Time in force (GTC/IOC/FOK) | LIMIT/STOP |
| `reduceOnly()` | Close position only | Futures |
| `stopPrice(48000)` | Trigger price | Conditional |
| `workingType('MARK_PRICE')` | Trigger type | Futures |
| `newOrderRespType('FULL')` | Response format | All types |
| `test()` | Test order | All types |

---

## ⚠️ Error Handling

The library provides custom exceptions for different error types:

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
    // Authentication error (invalid key/signature)
    echo "Auth error: " . $e->getMessage();
} catch (RateLimitException $e) {
    // Request limit exceeded
    echo "Rate limit exceeded. Retry after: " . $e->getRetryAfter();
} catch (InsufficientBalanceException $e) {
    // Insufficient funds
    echo "Insufficient balance";
} catch (ApiException $e) {
    // API error (business logic)
    echo "API error: " . $e->getErrorCode() . " - " . $e->getMessage();
} catch (BingxException $e) {
    // Library general errors
    echo "BingX error: " . $e->getMessage();
}
```

---

## 🧪 Testing

### Install Dependencies

```bash
composer install --dev
```

### Setup Environment

```bash
cp tests/.env.example tests/.env
```

Fill `tests/.env`:

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

### Run Tests

```bash
# Only safe tests (read-only)
vendor/bin/phpunit

# All tests including dangerous operations
vendor/bin/phpunit --group dangerous

# Specific services
vendor/bin/phpunit tests/Integration/MarketServiceTest.php
vendor/bin/phpunit tests/Integration/AccountServiceTest.php
vendor/bin/phpunit tests/Integration/TradeServiceTest.php

# Unit tests only
vendor/bin/phpunit tests/Unit/
```

### Test Types

- **Unit tests** — check basic functionality without API calls
- **Integration tests** — check real BingX API endpoints
- **Safe tests** (`@group safe`) — read-only operations
- **Dangerous tests** (`@group dangerous`) — data-modifying operations

---

## 📊 Library Statistics

### Complete API Coverage

| Service | Methods | Status |
|---------|---------|--------|
| Market Service | 28 | ✅ |
| Account Service | 30 | ✅ |
| Trade Service | 41 | ✅ |
| Wallet Service | 6 | ✅ |
| Spot Account Service | 8 | ✅ |
| Contract Service | 3 | ✅ |
| Listen Key Service | 3 | ✅ |
| **Total** | **119+** | **100% Coverage** |

### Key Features

- ✅ All Market API endpoints
- ✅ Complete Account API
- ✅ Extended Trade API
- ✅ Wallet and Spot Account API
- ✅ WebSocket data streams
- ✅ OrderBuilder for complex orders
- ✅ Market sentiment analysis
- ✅ Asset management and dust conversion
- ✅ Historical data and statistics
- ✅ Complete security and error handling

---

## 📖 Documentation

- **BingX API** — [https://bingx-api.github.io/docs/](https://bingx-api.github.io/docs/)
- **GitHub Repository** — [https://github.com/tigusigalpa/bingx-php](https://github.com/tigusigalpa/bingx-php)
- **Issues & Support** — [GitHub Issues](https://github.com/tigusigalpa/bingx-php/issues)

---

## 🏷️ Versions

- **2.0.0** — Complete refactor: modular architecture, error handling, 100% API coverage
- **1.0.0** — Basic authentication and wrappers
- **0.1.0** — Initial release

---

## 👨‍💻 Author

- **Igor Sazonov** (`@tigusigalpa`)
- **Email:** [sovletig@gmail.com](mailto:sovletig@gmail.com)
- **GitHub:** [github.com/tigusigalpa](https://github.com/tigusigalpa)

---

## 📄 License

MIT License — see [LICENSE](LICENSE) file for details.

---

## 🤝 Contributing

Pull requests are welcome! Please ensure:

1. Code follows PSR-12
2. Tests are added for new functionality
3. Documentation is updated

### Getting Started with Development

```bash
# Fork the repository
git clone https://github.com/your-username/bingx-php.git
cd bingx-php

# Create feature branch
git checkout -b feature/YourFeature

# Make changes and add tests
# Run tests
vendor/bin/phpunit

# Commit and push
git commit -m "Add your feature"
git push origin feature/YourFeature

# Open Pull Request
```

---

<div align="center">

**⭐ If this library helped you, please star it on [GitHub](https://github.com/tigusigalpa/bingx-php)!**

**BingX PHP SDK** — Full-Featured Client for BingX API with 100% endpoint coverage and advanced trading capabilities.

</div>
