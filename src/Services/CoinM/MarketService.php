<?php

namespace Tigusigalpa\BingX\Services\CoinM;

use Tigusigalpa\BingX\Http\BaseHttpClient;

/**
 * Coin-M Perpetual Futures Market Service
 * 
 * Provides market data operations for Coin-Margined perpetual futures contracts.
 * Uses /openApi/cswap/v1/market/* endpoints.
 */
class MarketService
{
    protected BaseHttpClient $client;

    public function __construct(BaseHttpClient $client)
    {
        $this->client = $client;
    }

    /**
     * Get contract information
     * 
     * GET /openApi/cswap/v1/market/contracts
     * 
     * Returns information about all available Coin-M perpetual contracts including:
     * - Contract specifications
     * - Trading rules
     * - Minimum order sizes
     * - Price precision
     * 
     * @param string|null $symbol Optional symbol filter (e.g., 'BTC-USD')
     * @return array Contract information
     * 
     * @example
     * $contracts = $service->getContracts();
     * $btcContract = $service->getContracts('BTC-USD');
     */
    public function getContracts(?string $symbol = null): array
    {
        $params = [];
        if ($symbol) {
            $params['symbol'] = $symbol;
        }
        
        return $this->client->request('GET', '/openApi/cswap/v1/market/contracts', $params, false);
    }

    /**
     * Get current price and funding rate
     * 
     * GET /openApi/cswap/v1/market/ticker
     * 
     * Returns current market ticker including:
     * - Last price
     * - Funding rate
     * - Next funding time
     * - Mark price
     * - Index price
     * 
     * @param string $symbol Trading pair (e.g., 'BTC-USD')
     * @return array Ticker data
     * 
     * @example
     * $ticker = $service->getTicker('BTC-USD');
     * echo "Price: {$ticker['lastPrice']}, Funding: {$ticker['fundingRate']}";
     */
    public function getTicker(string $symbol): array
    {
        return $this->client->request('GET', '/openApi/cswap/v1/market/ticker', [
            'symbol' => $symbol
        ], false);
    }

    /**
     * Get open positions (open interest)
     * 
     * GET /openApi/cswap/v1/market/openPositions
     * 
     * Returns the total number of open positions for a symbol.
     * Open interest indicates market activity and liquidity.
     * 
     * @param string $symbol Trading pair (e.g., 'BTC-USD')
     * @return array Open interest data
     * 
     * @example
     * $openInterest = $service->getOpenPositions('BTC-USD');
     * echo "Open positions: {$openInterest['openInterest']}";
     */
    public function getOpenPositions(string $symbol): array
    {
        return $this->client->request('GET', '/openApi/cswap/v1/market/openPositions', [
            'symbol' => $symbol
        ], false);
    }

    /**
     * Get K-line (candlestick) data
     * 
     * GET /openApi/cswap/v1/market/kline
     * 
     * Returns historical candlestick data for technical analysis.
     * 
     * @param string $symbol Trading pair (e.g., 'BTC-USD')
     * @param string $interval Time interval: 1m, 3m, 5m, 15m, 30m, 1h, 2h, 4h, 6h, 12h, 1d, 3d, 1w, 1M
     * @param int $limit Number of candles (default: 500, max: 1440)
     * @param int|null $startTime Start time in milliseconds
     * @param int|null $endTime End time in milliseconds
     * @return array Array of candlestick data
     * 
     * @example
     * // Get last 100 hourly candles
     * $klines = $service->getKlines('BTC-USD', '1h', 100);
     * 
     * // Get candles for specific time range
     * $klines = $service->getKlines(
     *     'BTC-USD', 
     *     '1h', 
     *     500,
     *     strtotime('2024-01-01') * 1000,
     *     strtotime('2024-01-02') * 1000
     * );
     */
    public function getKlines(
        string $symbol, 
        string $interval, 
        int $limit = 500,
        ?int $startTime = null,
        ?int $endTime = null
    ): array {
        $params = [
            'symbol' => $symbol,
            'interval' => $interval,
            'limit' => $limit
        ];
        
        if ($startTime) {
            $params['startTime'] = $startTime;
        }
        if ($endTime) {
            $params['endTime'] = $endTime;
        }
        
        return $this->client->request('GET', '/openApi/cswap/v1/market/kline', $params, false);
    }

    /**
     * Get order book depth
     * 
     * GET /openApi/cswap/v1/market/depth
     * 
     * Returns current order book with bids and asks.
     * 
     * @param string $symbol Trading pair (e.g., 'BTC-USD')
     * @param int $limit Depth limit: 5, 10, 20, 50, 100, 500, 1000 (default: 20)
     * @return array Order book data with 'bids' and 'asks' arrays
     * 
     * @example
     * $depth = $service->getDepth('BTC-USD', 20);
     * foreach ($depth['bids'] as $bid) {
     *     echo "Bid: {$bid[0]} @ {$bid[1]}\n";
     * }
     */
    public function getDepth(string $symbol, int $limit = 20): array
    {
        return $this->client->request('GET', '/openApi/cswap/v1/market/depth', [
            'symbol' => $symbol,
            'limit' => $limit
        ], false);
    }

    /**
     * Get 24-hour price change statistics
     * 
     * GET /openApi/cswap/v1/market/ticker/24hr
     * 
     * Returns 24-hour rolling window price change statistics.
     * 
     * @param string|null $symbol Trading pair (e.g., 'BTC-USD'). If null, returns all symbols.
     * @return array 24-hour statistics including:
     *               - priceChange: Absolute price change
     *               - priceChangePercent: Percentage price change
     *               - highPrice: Highest price
     *               - lowPrice: Lowest price
     *               - volume: Trading volume
     *               - quoteVolume: Quote asset volume
     * 
     * @example
     * // Single symbol
     * $stats = $service->get24hrPriceChange('BTC-USD');
     * echo "24h change: {$stats['priceChangePercent']}%";
     * 
     * // All symbols
     * $allStats = $service->get24hrPriceChange();
     */
    public function get24hrPriceChange(?string $symbol = null): array
    {
        $params = [];
        if ($symbol) {
            $params['symbol'] = $symbol;
        }
        
        return $this->client->request('GET', '/openApi/cswap/v1/market/ticker/24hr', $params, false);
    }
}
