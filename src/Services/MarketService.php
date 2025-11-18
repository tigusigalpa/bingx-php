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
     * @param int|null $startTime Start timestamp in milliseconds
     * @param int|null $endTime End timestamp in milliseconds
     * @return array
     */
    public function getPremiumIndexKlines(string $symbol, string $interval = '1h', int $limit = 100, ?int $startTime = null, ?int $endTime = null): array
    {
        $params = [
            'symbol' => $symbol,
            'interval' => $interval,
            'limit' => $limit
        ];

        if ($startTime) $params['startTime'] = $startTime;
        if ($endTime) $params['endTime'] = $endTime;

        return $this->client->request('GET', '/openApi/swap/v2/market/premiumIndexKline', $params);
    }

    /**
     * Get aggregate trades (recent trades list)
     * 
     * @param string $symbol Trading symbol
     * @param int $limit Number of records (1-1000)
     * @param int|null $fromId Trade ID to fetch from
     * @param int|null $startTime Start timestamp in milliseconds
     * @param int|null $endTime End timestamp in milliseconds
     * @return array
     */
    public function getAggregateTrades(string $symbol, int $limit = 500, ?int $fromId = null, ?int $startTime = null, ?int $endTime = null): array
    {
        $params = [
            'symbol' => $symbol,
            'limit' => $limit
        ];

        if ($fromId) $params['fromId'] = $fromId;
        if ($startTime) $params['startTime'] = $startTime;
        if ($endTime) $params['endTime'] = $endTime;

        return $this->client->request('GET', '/openApi/swap/v2/market/aggTrades', $params);
    }

    /**
     * Get recent trades list
     * 
     * @param string $symbol Trading symbol
     * @param int $limit Number of records (1-1000)
     * @return array
     */
    public function getRecentTrades(string $symbol, int $limit = 500): array
    {
        return $this->client->request('GET', '/openApi/swap/v2/market/trades', [
            'symbol' => $symbol,
            'limit' => $limit
        ]);
    }

    /**
     * Get spot aggregate trades
     * 
     * @param string $symbol Trading symbol
     * @param int $limit Number of records (1-1000)
     * @param int|null $fromId Trade ID to fetch from
     * @return array
     */
    public function getSpotAggregateTrades(string $symbol, int $limit = 500, ?int $fromId = null): array
    {
        $params = [
            'symbol' => $symbol,
            'limit' => $limit
        ];

        if ($fromId) $params['fromId'] = $fromId;

        return $this->client->request('GET', '/openApi/spot/v1/market/aggTrades', $params);
    }

    /**
     * Get spot recent trades list
     * 
     * @param string $symbol Trading symbol
     * @param int $limit Number of records (1-1000)
     * @return array
     */
    public function getSpotRecentTrades(string $symbol, int $limit = 500): array
    {
        return $this->client->request('GET', '/openApi/spot/v1/market/trades', [
            'symbol' => $symbol,
            'limit' => $limit
        ]);
    }

    /**
     * Get server time
     * 
     * @return array Server time information
     */
    public function getServerTime(): array
    {
        return $this->client->request('GET', '/openApi/swap/v2/market/time');
    }

    /**
     * Get spot server time
     * 
     * @return array Server time information
     */
    public function getSpotServerTime(): array
    {
        return $this->client->request('GET', '/openApi/spot/v1/market/time');
    }

    /**
     * Get continuous contract kline data
     * 
     * @param string $symbol Trading symbol
     * @param string $interval Time interval
     * @param int $limit Number of records
     * @param int|null $startTime Start timestamp in milliseconds
     * @param int|null $endTime End timestamp in milliseconds
     * @return array
     */
    public function getContinuousKlines(string $symbol, string $interval = '1h', int $limit = 100, ?int $startTime = null, ?int $endTime = null): array
    {
        $params = [
            'symbol' => $symbol,
            'interval' => $interval,
            'limit' => $limit
        ];

        if ($startTime) $params['startTime'] = $startTime;
        if ($endTime) $params['endTime'] = $endTime;

        return $this->client->request('GET', '/openApi/swap/v2/market/continuousKline', $params);
    }

    /**
     * Get index price kline data
     * 
     * @param string $symbol Trading symbol
     * @param string $interval Time interval
     * @param int $limit Number of records
     * @param int|null $startTime Start timestamp in milliseconds
     * @param int|null $endTime End timestamp in milliseconds
     * @return array
     */
    public function getIndexPriceKlines(string $symbol, string $interval = '1h', int $limit = 100, ?int $startTime = null, ?int $endTime = null): array
    {
        $params = [
            'symbol' => $symbol,
            'interval' => $interval,
            'limit' => $limit
        ];

        if ($startTime) $params['startTime'] = $startTime;
        if ($endTime) $params['endTime'] = $endTime;

        return $this->client->request('GET', '/openApi/swap/v2/market/indexPriceKline', $params);
    }

    /**
     * Get top long/short ratio
     * 
     * @param string $symbol Trading symbol
     * @param int $limit Number of records (1-30)
     * @return array
     */
    public function getTopLongShortRatio(string $symbol, int $limit = 10): array
    {
        return $this->client->request('GET', '/openApi/swap/v2/market/topLongShortRatio', [
            'symbol' => $symbol,
            'limit' => $limit
        ]);
    }

    /**
     * Get top traders position ratio
     * 
     * @param string $symbol Trading symbol
     * @param int $limit Number of records (1-30)
     * @return array
     */
    public function getTopTradersPositionRatio(string $symbol, int $limit = 10): array
    {
        return $this->client->request('GET', '/openApi/swap/v2/market/topTraderPositionRatio', [
            'symbol' => $symbol,
            'limit' => $limit
        ]);
    }

    /**
     * Get historical top long/short ratio
     * 
     * @param string $symbol Trading symbol
     * @param int $limit Number of records (1-500)
     * @param int|null $startTime Start timestamp in milliseconds
     * @param int|null $endTime End timestamp in milliseconds
     * @return array
     */
    public function getHistoricalTopLongShortRatio(string $symbol, int $limit = 500, ?int $startTime = null, ?int $endTime = null): array
    {
        $params = [
            'symbol' => $symbol,
            'limit' => $limit
        ];

        if ($startTime) $params['startTime'] = $startTime;
        if ($endTime) $params['endTime'] = $endTime;

        return $this->client->request('GET', '/openApi/swap/v2/market/topLongShortAccount', $params);
    }

    /**
     * Get top traders long/short ratio
     * 
     * @param string $symbol Trading symbol
     * @param int $limit Number of records (1-500)
     * @param int|null $startTime Start timestamp in milliseconds
     * @param int|null $endTime End timestamp in milliseconds
     * @return array
     */
    public function getTopTradersLongShortRatio(string $symbol, int $limit = 500, ?int $startTime = null, ?int $endTime = null): array
    {
        $params = [
            'symbol' => $symbol,
            'limit' => $limit
        ];

        if ($startTime) $params['startTime'] = $startTime;
        if ($endTime) $params['endTime'] = $endTime;

        return $this->client->request('GET', '/openApi/swap/v2/market/topLongShortPosition', $params);
    }

    /**
     * Get basis data
     * 
     * @param string $symbol Trading symbol
     * @param string $contractType Contract type (CURRENT_QUARTER, NEXT_QUARTER, PERPETUAL)
     * @param int $limit Number of records (1-500)
     * @param int|null $startTime Start timestamp in milliseconds
     * @param int|null $endTime End timestamp in milliseconds
     * @return array
     */
    public function getBasis(string $symbol, string $contractType = 'PERPETUAL', int $limit = 100, ?int $startTime = null, ?int $endTime = null): array
    {
        $params = [
            'symbol' => $symbol,
            'contractType' => $contractType,
            'limit' => $limit
        ];

        if ($startTime) $params['startTime'] = $startTime;
        if ($endTime) $params['endTime'] = $endTime;

        return $this->client->request('GET', '/openApi/swap/v2/market/basis', $params);
    }
}
