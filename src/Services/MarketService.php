<?php

namespace Tigusigalpa\BingX\Services;

use Tigusigalpa\BingX\Http\BaseHttpClient;

class MarketService
{
    protected BaseHttpClient $client;

    public function __construct(BaseHttpClient $client)
    {
        $this->client = $client;
    }

    /**
     * Get futures/swap contract symbols
     * 
     * @return array
     */
    public function getFuturesSymbols(): array
    {
        return $this->client->request('GET', '/openApi/swap/v2/market/symbols');
    }

    /**
     * Get spot trading symbols
     * 
     * @return array
     */
    public function getSpotSymbols(): array
    {
        return $this->client->request('GET', '/openApi/spot/v1/market/symbols');
    }

    /**
     * Get all available symbols (both spot and futures)
     * 
     * @return array Combined array with 'spot' and 'futures' keys
     */
    public function getAllSymbols(): array
    {
        return [
            'spot' => $this->getSpotSymbols(),
            'futures' => $this->getFuturesSymbols()
        ];
    }

    /**
     * Get contract symbols (alias for getFuturesSymbols for backward compatibility)
     * 
     * @return array
     */
    public function getSymbols(): array
    {
        return $this->getFuturesSymbols();
    }

    /**
     * Get latest price for a symbol
     * 
     * @param string $symbol Trading symbol (e.g., "BTC-USDT")
     * @return array
     */
    public function getLatestPrice(string $symbol): array
    {
        return $this->client->request('GET', '/openApi/swap/v2/market/latestPrice', [
            'symbol' => $symbol
        ]);
    }

    /**
     * Get latest price for a spot symbol
     * 
     * @param string $symbol Trading symbol (e.g., "BTC-USDT")
     * @return array
     */
    public function getSpotLatestPrice(string $symbol): array
    {
        return $this->client->request('GET', '/openApi/spot/v1/market/ticker/price', [
            'symbol' => $symbol
        ]);
    }

    /**
     * Get market depth (order book)
     * 
     * @param string $symbol Trading symbol
     * @param int $limit Number of depth levels (5, 10, 20)
     * @return array
     */
    public function getDepth(string $symbol, int $limit = 20): array
    {
        return $this->client->request('GET', '/openApi/swap/v2/market/depth', [
            'symbol' => $symbol,
            'limit' => $limit
        ]);
    }

    /**
     * Get spot market depth (order book)
     * 
     * @param string $symbol Trading symbol
     * @param int $limit Number of depth levels (5, 10, 20, 50, 100, 500, 1000)
     * @return array
     */
    public function getSpotDepth(string $symbol, int $limit = 20): array
    {
        return $this->client->request('GET', '/openApi/spot/v1/market/depth', [
            'symbol' => $symbol,
            'limit' => $limit
        ]);
    }

    /**
     * Get K-line (candlestick) data
     * 
     * @param string $symbol Trading symbol
     * @param string $interval Time interval (1m, 5m, 15m, 30m, 1h, 4h, 1d)
     * @param int $limit Number of records (1-500)
     * @param int|null $startTime Start timestamp in milliseconds
     * @param int|null $endTime End timestamp in milliseconds
     * @return array
     */
    public function getKlines(string $symbol, string $interval = '1h', int $limit = 100, ?int $startTime = null, ?int $endTime = null): array
    {
        $params = [
            'symbol' => $symbol,
            'interval' => $interval,
            'limit' => $limit
        ];

        if ($startTime) $params['startTime'] = $startTime;
        if ($endTime) $params['endTime'] = $endTime;

        return $this->client->request('GET', '/openApi/swap/v2/market/kline', $params);
    }

    /**
     * Get spot K-line (candlestick) data
     * 
     * @param string $symbol Trading symbol
     * @param string $interval Time interval (1m, 3m, 5m, 15m, 30m, 1h, 2h, 4h, 6h, 8h, 12h, 1d, 3d, 1w, 1M)
     * @param int $limit Number of records (1-1000)
     * @param int|null $startTime Start timestamp in milliseconds
     * @param int|null $endTime End timestamp in milliseconds
     * @return array
     */
    public function getSpotKlines(string $symbol, string $interval = '1h', int $limit = 100, ?int $startTime = null, ?int $endTime = null): array
    {
        $params = [
            'symbol' => $symbol,
            'interval' => $interval,
            'limit' => $limit
        ];

        if ($startTime) $params['startTime'] = $startTime;
        if ($endTime) $params['endTime'] = $endTime;

        return $this->client->request('GET', '/openApi/spot/v1/market/klines', $params);
    }

    /**
     * Get 24hr ticker price change statistics for futures
     * 
     * @param string|null $symbol Trading symbol (null for all symbols)
     * @return array
     */
    public function get24hrTicker(?string $symbol = null): array
    {
        $params = [];
        if ($symbol) $params['symbol'] = $symbol;

        return $this->client->request('GET', '/openApi/swap/v2/market/ticker24hr', $params);
    }

    /**
     * Get 24hr ticker price change statistics for spot
     * 
     * @param string|null $symbol Trading symbol (null for all symbols)
     * @return array
     */
    public function getSpot24hrTicker(?string $symbol = null): array
    {
        $params = [];
        if ($symbol) $params['symbol'] = $symbol;

        return $this->client->request('GET', '/openApi/spot/v1/market/ticker/24hr', $params);
    }

    /**
     * Get funding rate history
     * 
     * @param string $symbol Trading symbol
     * @param int $limit Number of records (1-100)
     * @return array
     */
    public function getFundingRateHistory(string $symbol, int $limit = 100): array
    {
        return $this->client->request('GET', '/openApi/swap/v2/market/fundingRate/history', [
            'symbol' => $symbol,
            'limit' => $limit
        ]);
    }

    /**
     * Get mark price
     * 
     * @param string $symbol Trading symbol
     * @return array
     */
    public function getMarkPrice(string $symbol): array
    {
        return $this->client->request('GET', '/openApi/swap/v2/market/markPrice', [
            'symbol' => $symbol
        ]);
    }

    /**
     * Get premium index K-line data
     * 
     * @param string $symbol Trading symbol
     * @param string $interval Time interval
     * @param int $limit Number of records
     * @return array
     */
    public function getPremiumIndexKlines(string $symbol, string $interval = '1h', int $limit = 100): array
    {
        return $this->client->request('GET', '/openApi/swap/v2/market/premiumIndexKline', [
            'symbol' => $symbol,
            'interval' => $interval,
            'limit' => $limit
        ]);
    }
}
