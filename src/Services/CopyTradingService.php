<?php

namespace Tigusigalpa\BingX\Services;

use Tigusigalpa\BingX\Http\BaseHttpClient;

class CopyTradingService
{
    protected BaseHttpClient $client;

    public function __construct(BaseHttpClient $client)
    {
        $this->client = $client;
    }

    /**
     * Get trader's current track orders for perpetual futures
     * 
     * @param string|null $symbol Trading pair symbol (optional)
     * @param int|null $recvWindow Receive window (optional)
     * @return array
     */
    public function getCurrentTrackOrders(?string $symbol = null, ?int $recvWindow = null): array
    {
        $params = [];
        
        if ($symbol !== null) $params['symbol'] = $symbol;
        if ($recvWindow !== null) $params['recvWindow'] = $recvWindow;

        return $this->client->request('GET', '/openApi/copyTrading/v1/swap/trace/currentTrack', $params);
    }

    /**
     * Close position by order number for perpetual futures
     * 
     * @param string $positionId Position ID to close
     * @param int|null $recvWindow Receive window (optional)
     * @return array
     */
    public function closeTrackOrder(string $positionId, ?int $recvWindow = null): array
    {
        $params = ['positionId' => $positionId];
        
        if ($recvWindow !== null) $params['recvWindow'] = $recvWindow;

        return $this->client->request('POST', '/openApi/copyTrading/v1/swap/trace/closeTrackOrder', $params);
    }

    /**
     * Set take profit and stop loss for perpetual futures position
     * 
     * @param string $positionId Position ID
     * @param float|null $stopLoss Stop loss price (optional)
     * @param float|null $takeProfit Take profit price (optional)
     * @param int|null $recvWindow Receive window (optional)
     * @return array
     */
    public function setTPSL(
        string $positionId,
        ?float $stopLoss = null,
        ?float $takeProfit = null,
        ?int $recvWindow = null
    ): array {
        $params = ['positionId' => $positionId];
        
        if ($stopLoss !== null) $params['stopLoss'] = $stopLoss;
        if ($takeProfit !== null) $params['takeProfit'] = $takeProfit;
        if ($recvWindow !== null) $params['recvWindow'] = $recvWindow;

        return $this->client->request('POST', '/openApi/copyTrading/v1/swap/trace/setTPSL', $params);
    }

    /**
     * Get personal trading overview for perpetual futures
     * 
     * @return array
     */
    public function getTraderDetail(): array
    {
        return $this->client->request('GET', '/openApi/copyTrading/v1/PFutures/traderDetail');
    }

    /**
     * Get profit summary for perpetual futures
     * 
     * @return array
     */
    public function getProfitSummary(): array
    {
        return $this->client->request('GET', '/openApi/copyTrading/v1/PFutures/profitHistorySummarys');
    }

    /**
     * Get profit details for perpetual futures
     * 
     * @param int|null $pageIndex Page index (default 1)
     * @param int|null $pageSize Page size (default 10, max 100)
     * @return array
     */
    public function getProfitDetail(?int $pageIndex = null, ?int $pageSize = null): array
    {
        $params = [];
        
        if ($pageIndex !== null) $params['pageIndex'] = $pageIndex;
        if ($pageSize !== null) $params['pageSize'] = $pageSize;

        return $this->client->request('GET', '/openApi/copyTrading/v1/PFutures/profitDetail', $params);
    }

    /**
     * Set commission rate for copy trading
     * 
     * @param float $commissionRate Commission rate (0-10)
     * @param int|null $recvWindow Receive window (optional)
     * @return array
     */
    public function setCommission(float $commissionRate, ?int $recvWindow = null): array
    {
        $params = ['commissionRate' => $commissionRate];
        
        if ($recvWindow !== null) $params['recvWindow'] = $recvWindow;

        return $this->client->request('POST', '/openApi/copyTrading/v1/PFutures/setCommission', $params);
    }

    /**
     * Get available copy trading pairs
     * 
     * @return array
     */
    public function getTradingPairs(): array
    {
        return $this->client->request('GET', '/openApi/copyTrading/v1/PFutures/tradingPairs');
    }

    /**
     * Sell spot assets based on buy order number
     * 
     * @param string $orderId Order ID to sell
     * @param int|null $recvWindow Receive window (optional)
     * @return array
     */
    public function sellSpotOrder(string $orderId, ?int $recvWindow = null): array
    {
        $params = ['orderId' => $orderId];
        
        if ($recvWindow !== null) $params['recvWindow'] = $recvWindow;

        return $this->client->request('POST', '/openApi/copyTrading/v1/spot/trader/sellOrder', $params);
    }

    /**
     * Get personal trading overview for spot
     * 
     * @return array
     */
    public function getSpotTraderDetail(): array
    {
        return $this->client->request('GET', '/openApi/copyTrading/v1/spot/traderDetail');
    }

    /**
     * Get profit summary for spot trading
     * 
     * @return array
     */
    public function getSpotProfitSummary(): array
    {
        return $this->client->request('GET', '/openApi/copyTrading/v1/spot/profitHistorySummarys');
    }

    /**
     * Get profit details for spot trading
     * 
     * @param int|null $pageIndex Page index (default 1)
     * @param int|null $pageSize Page size (default 10, max 100)
     * @return array
     */
    public function getSpotProfitDetail(?int $pageIndex = null, ?int $pageSize = null): array
    {
        $params = [];
        
        if ($pageIndex !== null) $params['pageIndex'] = $pageIndex;
        if ($pageSize !== null) $params['pageSize'] = $pageSize;

        return $this->client->request('GET', '/openApi/copyTrading/v1/spot/profitDetail', $params);
    }

    /**
     * Query historical orders for spot trading
     * 
     * @param int|null $pageIndex Page index (default 1)
     * @param int|null $pageSize Page size (default 50, max 100)
     * @return array
     */
    public function getSpotHistoryOrders(?int $pageIndex = null, ?int $pageSize = null): array
    {
        $params = [];
        
        if ($pageIndex !== null) $params['pageIndex'] = $pageIndex;
        if ($pageSize !== null) $params['pageSize'] = $pageSize;

        return $this->client->request('GET', '/openApi/copyTrading/v1/spot/historyOrder', $params);
    }
}
