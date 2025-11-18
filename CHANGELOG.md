# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [2.0.0] - 2025-11-18

### 🚀 Major Changes
- **Complete architectural refactor** - modular service-based architecture
- **Full BingX API coverage** - Market, Account, and Trade endpoints
- **Advanced error handling** - custom exception hierarchy
- **Enhanced Laravel integration** - improved service provider and facade

### ✨ Added
- **BaseHttpClient** - core HTTP client with improved signature handling
- **MarketService** - comprehensive market data operations:
  - `getSymbols()` - all trading symbols
  - `getLatestPrice()` - current price for symbol
  - `getDepth()` - order book data
  - `getKlines()` - candlestick data
  - `get24hrTicker()` - 24h statistics
  - `getFundingRateHistory()` - funding rates
  - `getMarkPrice()` - mark price data
  - `getPremiumIndexKlines()` - premium index data
- **AccountService** - account management operations:
  - `getBalance()` - account balance
  - `getPositions()` - open positions
  - `getAccountInfo()` - account information
  - `getTradingFees()` - trading fees
  - `getMarginMode()` / `setMarginMode()` - margin mode management
  - `getLeverage()` / `setLeverage()` - leverage management
  - `getPositionMargin()` / `setPositionMargin()` - position margin
  - `getBalanceHistory()` - balance change history
- **TradeService** - trading operations:
  - `createOrder()` - single order creation
  - `createBatchOrders()` - batch order creation
  - `cancelOrder()` - cancel single order
  - `cancelAllOrders()` - cancel all orders
  - `cancelBatchOrders()` - batch cancellation
  - `getOrder()` - order details
  - `getOpenOrders()` - open orders
  - `getOrderHistory()` - order history
  - `getFilledOrders()` - filled orders
  - `getUserTrades()` - user trades
  - `changeLeverage()` - leverage changes
  - `changeMarginType()` - margin type changes
  - `modifyPositionMargin()` - position margin modifications

### 🛡️ Security & Error Handling
- **Custom exception hierarchy**:
  - `BingxException` - base exception class
  - `ApiException` - API business logic errors
  - `AuthenticationException` - auth/permission errors
  - `RateLimitException` - rate limiting errors
  - `InsufficientBalanceException` - balance errors
- **Enhanced HTTP error handling** - proper Guzzle exception handling
- **API response validation** - automatic error detection from BingX responses
- **Improved signature generation** - better timestamp handling

### 🔧 Technical Improvements
- **Composition over inheritance** - services use injected HTTP client
- **Better timeout handling** - configurable timeouts for different scenarios
- **Enhanced logging** - detailed error information with API responses
- **Type safety improvements** - proper type hints throughout codebase
- **PSR-12 compliance** - consistent code formatting

### 📚 Documentation
- **Comprehensive README** - detailed usage examples for all services
- **Error handling guide** - exception handling examples
- **API reference** - links to official BingX documentation
- **Migration guide** - backward compatibility information

### 🔄 Backward Compatibility
- **Legacy methods preserved** - existing code continues to work
- **Gradual migration path** - can adopt new architecture incrementally

### 🐛 Fixed
- **Timestamp handling** - proper millisecond timestamp generation
- **Query parameter sorting** - correct parameter ordering for signatures
- **Header management** - proper API key and source key headers
- **JSON parsing** - robust JSON response handling

## [1.0.0] - 2025-11-18

### ✨ Added
- Basic HMAC-SHA256 signature generation
- Laravel service provider integration
- Facade support for easy access
- Publishable configuration file
- Basic API wrappers:
  - `getBalance()` - account balance
  - `getSymbols()` - trading symbols
  - `createOrder()` - order creation

### 🔧 Technical
- Guzzle HTTP client integration
- Environment-based configuration
- PSR-4 autoloading
- Laravel 8-12 compatibility

## [0.1.0] - 2025-11-18

### ✨ Added
- Initial package scaffolding
- Base authentication structure
- Basic HTTP client setup
- Composer configuration

---

## Migration Guide from 1.x to 2.x

### New Service-Based Architecture
```php
// Old way (still works)
$balance = Bingx::getBalance();
$symbols = Bingx::getSymbols();

// New recommended way
$balance = Bingx::account()->getBalance();
$symbols = Bingx::market()->getSymbols();
```

### Error Handling
```php
// New exception handling
try {
    $balance = Bingx::account()->getBalance();
} catch (AuthenticationException $e) {
    // Handle auth errors
} catch (RateLimitException $e) {
    // Handle rate limiting
}
```

### Full API Coverage
All BingX Swap V2 API endpoints are now available through the modular service architecture. See the README for complete usage examples.