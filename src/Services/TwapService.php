<?php

namespace Tigusigalpa\BingX\Services;

use Tigusigalpa\BingX\Http\BaseHttpClient;

class TwapService
{
    protected BaseHttpClient $client;

    public function __construct(BaseHttpClient $client)
    {
        $this->client = $client;
    }

    /**
     * Create TWAP order
     * 
     * TWAP (Time-Weighted Average Price) orders execute large trades over time
     * to minimize market impact by breaking them into smaller pieces.
     * 
     * @param array $params Order parameters
     * - symbol: Trading pair (e.g., "BTC-USDT")
     * - side: "BUY" or "SELL"
     * - positionSide: "LONG" or "SHORT"
     * - quantity: Total quantity to execute
     * - duration: Total execution time in seconds
     * - price: Limit price (optional, null for market price)
     * - clientOrderId: Custom order ID (optional)
     * @return array
     */
    public function createOrder(array $params): array
    {
        return $this->client->request('POST', '/openApi/swap/v1/trade/order/twap', $params);
    }

    /**
     * Quick TWAP buy order
     * 
     * @param string $symbol Trading symbol
     * @param float $quantity Total quantity
     * @param int $duration Duration in seconds
     * @param float|null $price Limit price (null for market)
     * @param string $positionSide Position side (LONG/SHORT)
     * @return array
     */
    public function buy(
        string $symbol,
        float $quantity,
        int $duration,
        ?float $price = null,
        string $positionSide = 'LONG'
    ): array {
        $params = [
            'symbol' => $symbol,
            'side' => 'BUY',
            'positionSide' => $positionSide,
            'quantity' => $quantity,
            'duration' => $duration,
        ];

        if ($price !== null) {
            $params['price'] = $price;
        }

        return $this->createOrder($params);
    }

    /**
     * Quick TWAP sell order
     * 
     * @param string $symbol Trading symbol
     * @param float $quantity Total quantity
     * @param int $duration Duration in seconds
     * @param float|null $price Limit price (null for market)
     * @param string $positionSide Position side (LONG/SHORT)
     * @return array
     */
    public function sell(
        string $symbol,
        float $quantity,
        int $duration,
        ?float $price = null,
        string $positionSide = 'SHORT'
    ): array {
        $params = [
            'symbol' => $symbol,
            'side' => 'SELL',
            'positionSide' => $positionSide,
            'quantity' => $quantity,
            'duration' => $duration,
        ];

        if ($price !== null) {
            $params['price'] = $price;
        }

        return $this->createOrder($params);
    }

    /**
     * Cancel TWAP order
     * 
     * @param string $orderId TWAP order ID
     * @param string $symbol Trading symbol
     * @return array
     */
    public function cancelOrder(string $orderId, string $symbol): array
    {
        return $this->client->request('DELETE', '/openApi/swap/v1/trade/order/twap', [
            'orderId' => $orderId,
            'symbol' => $symbol,
        ]);
    }

    /**
     * Get open TWAP orders
     * 
     * @param string|null $symbol Trading symbol (optional)
     * @param int $limit Number of records (default: 100)
     * @return array
     */
    public function getOpenOrders(?string $symbol = null, int $limit = 100): array
    {
        $params = ['limit' => $limit];
        if ($symbol) {
            $params['symbol'] = $symbol;
        }

        return $this->client->request('GET', '/openApi/swap/v1/trade/openOrders/twap', $params);
    }

    /**
     * Get TWAP order history
     * 
     * @param string|null $symbol Trading symbol (optional)
     * @param int $limit Number of records (default: 100)
     * @param int|null $startTime Start time in milliseconds
     * @param int|null $endTime End time in milliseconds
     * @return array
     */
    public function getHistoryOrders(
        ?string $symbol = null,
        int $limit = 100,
        ?int $startTime = null,
        ?int $endTime = null
    ): array {
        $params = ['limit' => $limit];
        
        if ($symbol) {
            $params['symbol'] = $symbol;
        }
        if ($startTime) {
            $params['startTime'] = $startTime;
        }
        if ($endTime) {
            $params['endTime'] = $endTime;
        }

        return $this->client->request('GET', '/openApi/swap/v1/trade/historyOrders/twap', $params);
    }

    /**
     * Get TWAP order details
     * 
     * @param string $orderId TWAP order ID
     * @return array
     */
    public function getOrderDetail(string $orderId): array
    {
        return $this->client->request('GET', '/openApi/swap/v1/trade/order/twap', [
            'orderId' => $orderId,
        ]);
    }

    /**
     * Get all TWAP orders (alias for getHistoryOrders)
     * 
     * @param string|null $symbol Trading symbol
     * @param string|null $status Order status (WORKING, FINISHED, CANCELLED)
     * @param int|null $startTime Start time in milliseconds
     * @param int|null $endTime End time in milliseconds
     * @param int $limit Number of records
     * @return array
     */
    public function getAllOrders(
        ?string $symbol = null,
        ?string $status = null,
        ?int $startTime = null,
        ?int $endTime = null,
        int $limit = 100
    ): array {
        $params = ['limit' => $limit];
        
        if ($symbol) {
            $params['symbol'] = $symbol;
        }
        if ($status) {
            $params['status'] = $status;
        }
        if ($startTime) {
            $params['startTime'] = $startTime;
        }
        if ($endTime) {
            $params['endTime'] = $endTime;
        }

        return $this->client->request('GET', '/openApi/swap/v1/trade/allOrders/twap', $params);
    }
}
