# BingX API v3 Migration Guide

## Overview

The bingx-php library has been updated to support BingX API v3, bringing institutional-grade trading features while maintaining full backward compatibility. This guide helps you understand the new features and how to use them.

## What's New in API v3

### 1. TWAP Orders (Time-Weighted Average Price)

Execute large trades over time to minimize market impact.

```php
// Execute 10 BTC over 1 hour
$twap = Bingx::twap()->buy(
    symbol: 'BTC-USDT',
    quantity: 10.0,
    duration: 3600,  // seconds
    positionSide: 'LONG'
);

// Monitor progress
$details = Bingx::twap()->getOrderDetail($twap['orderId']);
echo "Progress: {$details['executedQty']}/{$details['totalQty']}\n";

// Cancel if needed
Bingx::twap()->cancelOrder($twap['orderId'], 'BTC-USDT');
```

### 2. New Order Types

#### Trailing Stop Market
Dynamic stop-loss that follows price movements:

```php
$order = Bingx::trade()->order()
    ->futures()
    ->symbol('ETH-USDT')
    ->sell()
    ->long()
    ->type('TRAILING_STOP_MARKET')
    ->activationPrice(3000.0)    // Start trailing at $3000
    ->callbackRate(1.0)           // Trail 1% behind peak
    ->quantity(1.0)
    ->execute();
```

#### Trigger Limit
Trigger order executed as limit (not market):

```php
$order = Bingx::trade()->order()
    ->futures()
    ->symbol('BTC-USDT')
    ->buy()
    ->long()
    ->type('TRIGGER_LIMIT')
    ->stopPrice(49500.0)    // Trigger price
    ->price(50000.0)        // Limit price
    ->margin(100)
    ->leverage(10)
    ->execute();
```

#### Trailing TP/SL
Combined trailing take-profit and stop-loss:

```php
$order = Bingx::trade()->order()
    ->futures()
    ->symbol('BTC-USDT')
    ->buy()
    ->long()
    ->type('TRAILING_TP_SL')
    ->quantity(0.1)
    ->takeProfitPrice(52000.0)
    ->stopLossPrice(48000.0)
    ->trailingStopPercent(0.5)  // 0.5% trailing
    ->execute();
```

### 3. Multi-Assets Margin Mode

Use your entire portfolio as collateral:

```php
// Enable multi-assets margin
Bingx::trade()->switchMultiAssetsMode(true);

// Check status
$mode = Bingx::trade()->getMultiAssetsMode();

// Get margin rules
$rules = Bingx::trade()->getMultiAssetsRules();

// View margin details
$margin = Bingx::trade()->getMultiAssetsMargin();
```

**Benefits:**
- Lower margin requirements for hedged positions
- More efficient capital usage
- Better for portfolio strategies

### 4. One-Click Position Reversal

Flip position direction atomically:

```php
// Reverse: LONG → SHORT or SHORT → LONG
Bingx::trade()->oneClickReversePosition('BTC-USDT');
```

**Use cases:**
- Trend reversal strategies
- Quick position flips
- "Stop and reverse" systems

### 5. Auto-Add Margin

Automatically add margin when approaching liquidation:

```php
// Enable for LONG positions
Bingx::trade()->setAutoAddMargin('BTC-USDT', 'LONG', true);

// Check status
$status = Bingx::trade()->getAutoAddMargin('BTC-USDT', 'LONG');
```

**Note:** Only works in hedge mode.

### 6. Position Risk Monitoring

Track liquidation risk in real-time:

```php
$risk = Bingx::account()->getPositionRisk('BTC-USDT');

echo "Liquidation Price: {$risk['liquidationPrice']}\n";
echo "Margin Ratio: {$risk['marginRatio']}\n";
echo "Unrealized P&L: {$risk['unrealizedProfit']}\n";
echo "Leverage: {$risk['leverage']}\n";
```

### 7. Income & P&L Tracking

Detailed profit/loss breakdown:

```php
// Get income history by type
$income = Bingx::account()->getIncomeHistory(
    symbol: 'BTC-USDT',
    incomeType: 'REALIZED_PNL',
    limit: 100
);

// Income types:
// - REALIZED_PNL: Actual profit/loss from closed trades
// - FUNDING_FEE: 8-hour funding payments
// - COMMISSION: Trading fees
// - TRANSFER: Account transfers
// - INSURANCE_CLEAR: Insurance fund events

// Commission history
$commissions = Bingx::account()->getCommissionHistory('BTC-USDT');
```

### 8. Enhanced Market Data

#### Open Interest
```php
// Current open interest
$oi = Bingx::market()->getOpenInterest('BTC-USDT');

// Historical open interest
$oiHistory = Bingx::market()->getOpenInterestHistory(
    symbol: 'BTC-USDT',
    period: '5m',  // 5m, 15m, 30m, 1h, 4h, 1d
    limit: 100
);
```

#### Funding Rate Info
```php
$funding = Bingx::market()->getFundingRateInfo('BTC-USDT');
echo "Current Rate: {$funding['fundingRate']}\n";
echo "Next Payment: {$funding['fundingTime']}\n";
```

#### Book Ticker (Best Bid/Ask)
```php
$ticker = Bingx::market()->getBookTicker('BTC-USDT');
echo "Best Bid: {$ticker['bidPrice']} @ {$ticker['bidQty']}\n";
echo "Best Ask: {$ticker['askPrice']} @ {$ticker['askQty']}\n";
```

## Backward Compatibility

**All existing code continues to work without changes.** API v3 features are opt-in:

```php
// Your existing code works as before
$balance = Bingx::account()->getBalance();
$order = Bingx::trade()->createOrder([
    'symbol' => 'BTC-USDT',
    'side' => 'BUY',
    'type' => 'MARKET',
    'quantity' => 0.001
]);

// Use v3 features when ready
$twap = Bingx::twap()->buy('BTC-USDT', 10.0, 3600);
$risk = Bingx::account()->getPositionRisk('BTC-USDT');
```

## Complete Feature List

### New Services
- `TwapService` - TWAP order management

### TradeService New Methods
- `switchMultiAssetsMode()` / `getMultiAssetsMode()`
- `getMultiAssetsRules()` / `getMultiAssetsMargin()`
- `oneClickReversePosition()`
- `setAutoAddMargin()` / `getAutoAddMargin()`

### AccountService New Methods
- `getPositionRisk()`
- `getIncomeHistory()`
- `getCommissionHistory()`
- `setPositionMode()` / `getPositionModeV3()`

### MarketService New Methods
- `getOpenInterest()` / `getOpenInterestHistory()`
- `getFundingRateInfo()`
- `getBookTicker()`
- `getIndexPrice()`
- `getTickerPrice()`

### OrderBuilder New Features
- Order types: `TRAILING_STOP_MARKET`, `TRIGGER_LIMIT`, `TRAILING_TP_SL`
- Parameters: `activationPrice()`, `callbackRate()`, `trailingStopPercent()`
- Parameters: `takeProfitPrice()`, `stopLossPrice()`

## Best Practices

### TWAP Orders
- Use for orders > 1% of daily volume
- Duration should be at least 10 minutes
- Monitor progress with `getOrderDetail()`

### Trailing Stops
- Set activation price above/below entry
- Callback rate: 0.5-2% for most assets
- Test with small positions first

### Multi-Assets Margin
- Only enable if you understand portfolio margin
- Monitor total margin usage regularly
- Not recommended for beginners

### Risk Monitoring
- Check `getPositionRisk()` before adding to positions
- Set alerts when margin ratio > 80%
- Use `setAutoAddMargin()` as safety net

## Migration Checklist

- [ ] Review new features relevant to your strategy
- [ ] Test TWAP orders with small amounts
- [ ] Experiment with trailing stops in demo/testnet
- [ ] Set up risk monitoring alerts
- [ ] Update your trading bots to use new order types
- [ ] Enable multi-assets margin if beneficial
- [ ] Implement P&L tracking for better analytics

## Support

- **Documentation**: https://bingx-api.github.io/docs-v3/
- **GitHub Issues**: https://github.com/tigusigalpa/bingx-php/issues
- **Changelog**: See `CHANGELOG.md` for detailed changes

## Version Information

- **Library Version**: 2.0.0
- **API Version**: v3
- **PHP Requirements**: ^7.4|^8.0|^8.1|^8.2
- **Laravel Support**: 8-12
