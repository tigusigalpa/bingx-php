<?php

namespace Tigusigalpa\BingX\WebSocket;

/**
 * Market Data Stream
 * 
 * Provides WebSocket streaming for public market data
 */
class MarketDataStream extends WebSocketClient
{
    const URL = 'wss://open-api-swap.bingx.com/swap-market';

    public function __construct()
    {
        parent::__construct(self::URL);
    }

    /**
     * Subscribe to trade updates for a symbol
     * 
     * @param string $symbol Trading symbol (e.g., 'BTC-USDT')
     * @param string|null $id Optional request ID
     * @return void
     */
    public function subscribeTrade(string $symbol, ?string $id = null): void
    {
        $id = $id ?? $this->generateId();
        $this->subscribe($id, "{$symbol}@trade");
    }

    /**
     * Subscribe to kline/candlestick updates
     * 
     * @param string $symbol Trading symbol (e.g., 'BTC-USDT')
     * @param string $interval Kline interval (e.g., '1m', '5m', '1h', '1d')
     * @param string|null $id Optional request ID
     * @return void
     */
    public function subscribeKline(string $symbol, string $interval, ?string $id = null): void
    {
        $id = $id ?? $this->generateId();
        $this->subscribe($id, "{$symbol}@kline_{$interval}");
    }

    /**
     * Subscribe to depth/orderbook updates
     * 
     * @param string $symbol Trading symbol (e.g., 'BTC-USDT')
     * @param int $levels Depth levels (5, 10, 20, 50, 100)
     * @param string|null $id Optional request ID
     * @return void
     */
    public function subscribeDepth(string $symbol, int $levels = 20, ?string $id = null): void
    {
        $id = $id ?? $this->generateId();
        $this->subscribe($id, "{$symbol}@depth{$levels}");
    }

    /**
     * Subscribe to 24hr ticker updates
     * 
     * @param string $symbol Trading symbol (e.g., 'BTC-USDT')
     * @param string|null $id Optional request ID
     * @return void
     */
    public function subscribeTicker(string $symbol, ?string $id = null): void
    {
        $id = $id ?? $this->generateId();
        $this->subscribe($id, "{$symbol}@ticker");
    }

    /**
     * Subscribe to best bid/ask price updates
     * 
     * @param string $symbol Trading symbol (e.g., 'BTC-USDT')
     * @param string|null $id Optional request ID
     * @return void
     */
    public function subscribeBookTicker(string $symbol, ?string $id = null): void
    {
        $id = $id ?? $this->generateId();
        $this->subscribe($id, "{$symbol}@bookTicker");
    }

    /**
     * Unsubscribe from trade updates
     * 
     * @param string $symbol Trading symbol
     * @param string|null $id Optional request ID
     * @return void
     */
    public function unsubscribeTrade(string $symbol, ?string $id = null): void
    {
        $id = $id ?? $this->generateId();
        $this->unsubscribe($id, "{$symbol}@trade");
    }

    /**
     * Unsubscribe from kline updates
     * 
     * @param string $symbol Trading symbol
     * @param string $interval Kline interval
     * @param string|null $id Optional request ID
     * @return void
     */
    public function unsubscribeKline(string $symbol, string $interval, ?string $id = null): void
    {
        $id = $id ?? $this->generateId();
        $this->unsubscribe($id, "{$symbol}@kline_{$interval}");
    }

    /**
     * Unsubscribe from depth updates
     * 
     * @param string $symbol Trading symbol
     * @param int $levels Depth levels
     * @param string|null $id Optional request ID
     * @return void
     */
    public function unsubscribeDepth(string $symbol, int $levels = 20, ?string $id = null): void
    {
        $id = $id ?? $this->generateId();
        $this->unsubscribe($id, "{$symbol}@depth{$levels}");
    }

    /**
     * Unsubscribe from ticker updates
     * 
     * @param string $symbol Trading symbol
     * @param string|null $id Optional request ID
     * @return void
     */
    public function unsubscribeTicker(string $symbol, ?string $id = null): void
    {
        $id = $id ?? $this->generateId();
        $this->unsubscribe($id, "{$symbol}@ticker");
    }

    /**
     * Unsubscribe from book ticker updates
     * 
     * @param string $symbol Trading symbol
     * @param string|null $id Optional request ID
     * @return void
     */
    public function unsubscribeBookTicker(string $symbol, ?string $id = null): void
    {
        $id = $id ?? $this->generateId();
        $this->unsubscribe($id, "{$symbol}@bookTicker");
    }

    /**
     * Generate a unique request ID
     * 
     * @return string
     */
    protected function generateId(): string
    {
        return uniqid('bingx_', true);
    }
}
