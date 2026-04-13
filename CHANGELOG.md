# Changelog

All notable changes to this project will be documented in this file.

## [2.1.0] - 2026-04-13

### Added
- **Laravel 13 Support**: Full compatibility with Laravel 13
- **PHP 8.3/8.4 Support**: Added support for PHP 8.3 and 8.4

## [2.0.0] - 2026-04-05

### 🚀 Major Update: BingX API v3 Support

This release adds comprehensive support for BingX API v3, bringing institutional-grade trading features to the library while maintaining full backward compatibility with existing code.

### Added

#### 🎯 TWAP Service (Time-Weighted Average Price)
- **New Service**: `TwapService` for algorithmic order execution
  - `createOrder()` - Create TWAP orders to execute large trades over time
  - `buy()` / `sell()` - Quick TWAP order helpers
  - `cancelOrder()` - Cancel active TWAP orders
  - `getOpenOrders()` - Query open TWAP orders
  - `getHistoryOrders()` - TWAP order history
  - `getOrderDetail()` - Detailed TWAP order information
  - `getAllOrders()` - Query all TWAP orders with filters
- Minimizes market impact by breaking large orders into smaller pieces
- Accessible via `Bingx::twap()` facade method

#### 📊 TradeService - API v3 Methods
- **Multi-Assets Margin Mode**:
  - `switchMultiAssetsMode()` - Enable/disable portfolio margin
  - `getMultiAssetsMode()` - Query current mode status
  - `getMultiAssetsRules()` - Get margin calculation rules
  - `getMultiAssetsMargin()` - Detailed margin information
  - Reduces margin requirements for hedged positions

- **Position Management**:
  - `oneClickReversePosition()` - Atomically flip position direction (LONG ↔ SHORT)
  - `setAutoAddMargin()` - Auto-add margin when approaching liquidation
  - `getAutoAddMargin()` - Query auto-margin status

- **New Order Types** (via OrderBuilder):
  - `TRAILING_STOP_MARKET` - Dynamic stop-loss that follows price
  - `TRIGGER_LIMIT` - Trigger order executed as limit (not market)
  - `TRAILING_TP_SL` - Combined trailing take-profit and stop-loss

#### 💰 AccountService - API v3 Methods
- **Risk Monitoring**:
  - `getPositionRisk()` - Liquidation price, margin ratio, unrealized P&L
  - Monitor position health in real-time

- **Income & P&L Tracking**:
  - `getIncomeHistory()` - Detailed income records by type
    - Types: REALIZED_PNL, FUNDING_FEE, COMMISSION, TRANSFER, INSURANCE_CLEAR
  - `getCommissionHistory()` - Trading fee records
  - Track profitability across all positions

- **Position Mode Management**:
  - `setPositionMode()` - Switch between hedge mode and one-way mode
  - `getPositionModeV3()` - Query current position mode

#### 📈 MarketService - API v3 Methods
- **Open Interest Data**:
  - `getOpenInterest()` - Current open interest for symbol
  - `getOpenInterestHistory()` - Historical OI with customizable periods (5m, 15m, 30m, 1h, 4h, 1d)

- **Enhanced Market Data**:
  - `getFundingRateInfo()` - Current funding rate and next payment time
  - `getBookTicker()` - Best bid/ask without full order book
  - `getIndexPrice()` - Index price for symbol
  - `getTickerPrice()` - Latest price for symbol(s)

#### 🔧 OrderBuilder Enhancements
- **New Order Type Support**:
  - Added `TRAILING_STOP_MARKET`, `TRIGGER_LIMIT`, `TRAILING_TP_SL` to valid types

- **New Parameters**:
  - `activationPrice()` - Price to start trailing stop
  - `callbackRate()` - Trailing distance percentage (e.g., 1.0 for 1%)
  - `trailingStopPercent()` - Trailing stop percentage for TRAILING_TP_SL
  - `takeProfitPrice()` - Take profit price for TRAILING_TP_SL
  - `stopLossPrice()` - Stop loss price for TRAILING_TP_SL

### Examples

#### TWAP Order
```php
// Execute 10 BTC over 1 hour to minimize market impact
$twap = Bingx::twap()->buy(
    symbol: 'BTC-USDT',
    quantity: 10.0,
    duration: 3600,  // 1 hour in seconds
    positionSide: 'LONG'
);
```

#### Trailing Stop Market Order
```php
$order = Bingx::trade()->order()
    ->futures()
    ->symbol('ETH-USDT')
    ->sell()
    ->long()
    ->type('TRAILING_STOP_MARKET')
    ->activationPrice(3000.0)    // Start trailing at $3000
    ->callbackRate(1.0)           // Trail 1% behind high
    ->quantity(1.0)
    ->execute();
```

#### Multi-Assets Margin
```php
// Enable portfolio margin for optimized capital usage
Bingx::trade()->switchMultiAssetsMode(true);

// Check margin details
$margin = Bingx::trade()->getMultiAssetsMargin();
```

#### Position Risk Monitoring
```php
$risk = Bingx::account()->getPositionRisk('BTC-USDT');
echo "Liquidation Price: {$risk['liquidationPrice']}\n";
echo "Margin Ratio: {$risk['marginRatio']}\n";
```

#### One-Click Position Reversal
```php
// Flip from LONG to SHORT in one atomic operation
Bingx::trade()->oneClickReversePosition('BTC-USDT');
```

### Changed
- Updated `composer.json` description to reflect API v3 support
- Enhanced type safety across all new methods
- Improved parameter validation for v3 endpoints

### API Compatibility
- ✅ **Full backward compatibility** - All existing code continues to work
- ✅ **Opt-in v3 features** - Use new features when ready
- ✅ **Consistent API design** - New methods follow existing patterns

---

## [Unreleased] - 2026-03-15

### Added

#### Market Service
- **Spot Symbols Endpoint Update**: Updated `getSpotSymbols()` to use `/openApi/spot/v1/common/symbols` endpoint
  - Added support for `maxMarketNotional` field (maximum notional amount for single market order)
  - Added new symbol status value `29 = Pre-Delisted`
  - Full status values: 0=Offline, 1=Online, 5=Pre-open, 10=Accessed, 25=Suspended, 29=Pre-Delisted, 30=Delisted

- **Spot Klines v2 Endpoint**: Updated `getSpotKlines()` to use `/openApi/spot/v2/market/kline`
  - Added optional `timeZone` parameter (0=UTC (default), 8=UTC+8)
  - Updated max limit from 1000 to 1440 records

#### Spot Account Service
- **Internal Transfer Update**: Updated `internalTransfer()` method with new parameters
  - Changed `walletType` to integer: 1=Fund Account, 2=Standard Futures, 3=Perpetual Futures, **4=Spot Account** (NEW)
  - Added `userAccountType` parameter (1=UID, 2=Phone number, 3=Email)
  - Added `userAccount` parameter
  - Added optional `callingCode` parameter (required when userAccountType=2)
  - Added optional `transferClientId` parameter (custom ID, max 100 chars)
  - Added optional `recvWindow` parameter

#### Sub-Account Service
- **Sub-Account Internal Transfer Update**: Updated `subAccountInternalTransfer()` with new parameters
  - Changed `walletType` to integer: 1=Fund Account, 2=Standard Futures, 3=Perpetual Futures, **15=Spot Account** (NEW)
  - Added `userAccountType` parameter (1=UID, 2=Phone number, 3=Email)
  - Added `userAccount` parameter
  - Added optional `callingCode` parameter
  - Added optional `transferClientId` parameter
  - Added optional `recvWindow` parameter

- **New Method**: `subMotherAccountAssetTransfer()` - Sub-Mother Account Asset Transfer Interface
  - Flexible asset transfer between parent and sub-accounts
  - Supports account types: 1=Funding, 2=Standard futures, 3=Perpetual U-based, 15=Spot
  - Only available to master account
  - Returns `tranId` (transfer record ID)

- **New Method**: `getSubMotherAccountTransferableAmount()` - Query Sub-Mother Account Transferable Amount
  - Query supported coins and available transferable amounts
  - Only available to master account
  - Returns coins array with id, name, and availableAmount

- **New Method**: `getSubMotherAccountTransferHistory()` - Query Sub-Mother Account Transfer History
  - Query transfer history between sub-accounts and parent account
  - Supports filtering by type, tranId, time range
  - Pagination support (pageId, pagingSize)
  - Only available to master account

### Changed
- Updated BingX API integration to support changes from December 2025 through February 2026
- Improved parameter validation and type safety across all updated methods

### API Compatibility
- All changes maintain backward compatibility where possible
- New optional parameters added with sensible defaults
- Existing method signatures preserved for non-breaking changes

---

## API Reference
For detailed API documentation, see: https://bingx-api.github.io/docs-v3/
