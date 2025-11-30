<?php

namespace Tigusigalpa\BingX\Services\CoinM;

use Tigusigalpa\BingX\Http\BaseHttpClient;

/**
 * Coin-M Perpetual Futures Trade Service
 * 
 * Provides trading operations for Coin-Margined perpetual futures contracts.
 * Uses /openApi/cswap/v1/trade/* endpoints.
 */
class TradeService
{
    protected BaseHttpClient $client;

    public function __construct(BaseHttpClient $client)
    {
        $this->client = $client;
    }

    /**
     * Create a new order
     * 
     * POST /openApi/cswap/v1/trade/order
     * 
     * Places a new order for Coin-M perpetual futures.
     * 
     * @param array $params Order parameters:
     *                      - symbol (required): Trading pair (e.g., 'BTC-USD')
     *                      - side (required): 'BUY' or 'SELL'
     *                      - positionSide (required): 'LONG' or 'SHORT'
     *                      - type (required): 'LIMIT', 'MARKET', 'STOP_MARKET', 'TAKE_PROFIT_MARKET', 'STOP', 'TAKE_PROFIT'
     *                      - quantity (required): Order quantity in contracts
     *                      - price (conditional): Required for LIMIT orders
     *                      - stopPrice (conditional): Required for STOP orders
     *                      - timeInForce (optional): 'GTC', 'IOC', 'FOK' (default: 'GTC')
     *                      - clientOrderId (optional): Custom order ID
     *                      - reduceOnly (optional): true/false
     *                      - workingType (optional): 'MARK_PRICE' or 'CONTRACT_PRICE'
     *                      - recvWindow (optional): Request validity window
     * @return array Order response with orderId and status
     * 
     * @example
     * // Market order
     * $order = $service->createOrder([
     *     'symbol' => 'BTC-USD',
     *     'side' => 'BUY',
     *     'positionSide' => 'LONG',
     *     'type' => 'MARKET',
     *     'quantity' => 100
     * ]);
     * 
     * // Limit order with stop loss
     * $order = $service->createOrder([
     *     'symbol' => 'BTC-USD',
     *     'side' => 'BUY',
     *     'positionSide' => 'LONG',
     *     'type' => 'LIMIT',
     *     'quantity' => 100,
     *     'price' => 50000,
     *     'timeInForce' => 'GTC'
     * ]);
     */
    public function createOrder(array $params): array
    {
        return $this->client->request('POST', '/openApi/cswap/v1/trade/order', $params);
    }

    /**
     * Get commission rate
     * 
     * GET /openApi/cswap/v1/trade/commissionRate
     * 
     * Returns the trading commission rate for a symbol.
     * 
     * @param string $symbol Trading pair (e.g., 'BTC-USD')
     * @return array Commission rate data
     * 
     * @example
     * $rate = $service->getCommissionRate('BTC-USD');
     * echo "Maker: {$rate['makerCommissionRate']}, Taker: {$rate['takerCommissionRate']}";
     */
    public function getCommissionRate(string $symbol): array
    {
        return $this->client->request('GET', '/openApi/cswap/v1/trade/commissionRate', [
            'symbol' => $symbol
        ]);
    }

    /**
     * Query current leverage
     * 
     * GET /openApi/cswap/v1/trade/leverage
     * 
     * Returns the current leverage setting for a symbol.
     * 
     * @param string $symbol Trading pair (e.g., 'BTC-USD')
     * @param int|null $recvWindow Request validity window in milliseconds
     * @return array Leverage data
     * 
     * @example
     * $leverage = $service->getLeverage('BTC-USD');
     * echo "Current leverage: {$leverage['leverage']}x";
     */
    public function getLeverage(string $symbol, ?int $recvWindow = null): array
    {
        $params = ['symbol' => $symbol];
        if ($recvWindow) {
            $params['recvWindow'] = $recvWindow;
        }
        
        return $this->client->request('GET', '/openApi/cswap/v1/trade/leverage', $params);
    }

    /**
     * Modify leverage
     * 
     * POST /openApi/cswap/v1/trade/leverage
     * 
     * Changes the leverage for a symbol.
     * 
     * @param string $symbol Trading pair (e.g., 'BTC-USD')
     * @param int $leverage Leverage value (1-125, depending on symbol)
     * @param string|null $side Position side: 'LONG' or 'SHORT' (for hedge mode)
     * @param int|null $recvWindow Request validity window in milliseconds
     * @return array Updated leverage data
     * 
     * @example
     * // One-way mode
     * $result = $service->setLeverage('BTC-USD', 10);
     * 
     * // Hedge mode
     * $result = $service->setLeverage('BTC-USD', 10, 'LONG');
     */
    public function setLeverage(string $symbol, int $leverage, ?string $side = null, ?int $recvWindow = null): array
    {
        $params = [
            'symbol' => $symbol,
            'leverage' => $leverage
        ];
        
        if ($side) {
            $params['side'] = $side;
        }
        if ($recvWindow) {
            $params['recvWindow'] = $recvWindow;
        }
        
        return $this->client->request('POST', '/openApi/cswap/v1/trade/leverage', $params);
    }

    /**
     * Cancel all orders
     * 
     * DELETE /openApi/cswap/v1/trade/allOrders
     * 
     * Cancels all open orders for a symbol.
     * 
     * @param string $symbol Trading pair (e.g., 'BTC-USD')
     * @param int|null $recvWindow Request validity window in milliseconds
     * @return array Cancellation result
     * 
     * @example
     * $result = $service->cancelAllOrders('BTC-USD');
     */
    public function cancelAllOrders(string $symbol, ?int $recvWindow = null): array
    {
        $params = ['symbol' => $symbol];
        if ($recvWindow) {
            $params['recvWindow'] = $recvWindow;
        }
        
        return $this->client->request('DELETE', '/openApi/cswap/v1/trade/allOrders', $params);
    }

    /**
     * Close all positions
     * 
     * DELETE /openApi/cswap/v1/trade/closeAllPositions
     * 
     * Closes all open positions for a symbol using market orders.
     * 
     * @param string $symbol Trading pair (e.g., 'BTC-USD')
     * @param int|null $recvWindow Request validity window in milliseconds
     * @return array Close result
     * 
     * @example
     * $result = $service->closeAllPositions('BTC-USD');
     */
    public function closeAllPositions(string $symbol, ?int $recvWindow = null): array
    {
        $params = ['symbol' => $symbol];
        if ($recvWindow) {
            $params['recvWindow'] = $recvWindow;
        }
        
        return $this->client->request('DELETE', '/openApi/cswap/v1/trade/closeAllPositions', $params);
    }

    /**
     * Query positions
     * 
     * GET /openApi/cswap/v1/trade/positions
     * 
     * Returns current open positions.
     * 
     * @param string|null $symbol Trading pair (e.g., 'BTC-USD'). If null, returns all positions.
     * @param int|null $recvWindow Request validity window in milliseconds
     * @return array Position data including:
     *               - symbol: Trading pair
     *               - positionSide: LONG or SHORT
     *               - positionAmt: Position size
     *               - availableAmt: Available amount
     *               - unrealizedProfit: Unrealized P&L
     *               - leverage: Current leverage
     * 
     * @example
     * // All positions
     * $positions = $service->getPositions();
     * 
     * // Specific symbol
     * $btcPosition = $service->getPositions('BTC-USD');
     */
    public function getPositions(?string $symbol = null, ?int $recvWindow = null): array
    {
        $params = [];
        if ($symbol) {
            $params['symbol'] = $symbol;
        }
        if ($recvWindow) {
            $params['recvWindow'] = $recvWindow;
        }
        
        return $this->client->request('GET', '/openApi/cswap/v1/trade/positions', $params);
    }

    /**
     * Get account balance
     * 
     * GET /openApi/cswap/v1/trade/balance
     * 
     * Returns account balance for Coin-M futures.
     * 
     * @param int|null $recvWindow Request validity window in milliseconds
     * @return array Balance data for each asset:
     *               - asset: Asset name (e.g., 'BTC')
     *               - balance: Total balance
     *               - availableBalance: Available balance
     *               - equity: Account equity
     *               - unrealizedProfit: Unrealized P&L
     * 
     * @example
     * $balance = $service->getBalance();
     * foreach ($balance as $asset) {
     *     echo "{$asset['asset']}: {$asset['balance']}\n";
     * }
     */
    public function getBalance(?int $recvWindow = null): array
    {
        $params = [];
        if ($recvWindow) {
            $params['recvWindow'] = $recvWindow;
        }
        
        return $this->client->request('GET', '/openApi/cswap/v1/trade/balance', $params);
    }

    /**
     * Get force orders (liquidations)
     * 
     * GET /openApi/cswap/v1/trade/forceOrders
     * 
     * Returns liquidation order history.
     * 
     * @param string|null $symbol Trading pair (e.g., 'BTC-USD')
     * @param int|null $startTime Start time in milliseconds
     * @param int|null $endTime End time in milliseconds
     * @param int $limit Number of records (default: 50, max: 100)
     * @param int|null $recvWindow Request validity window in milliseconds
     * @return array Liquidation orders
     * 
     * @example
     * $liquidations = $service->getForceOrders('BTC-USD', null, null, 20);
     */
    public function getForceOrders(
        ?string $symbol = null,
        ?int $startTime = null,
        ?int $endTime = null,
        int $limit = 50,
        ?int $recvWindow = null
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
        if ($recvWindow) {
            $params['recvWindow'] = $recvWindow;
        }
        
        return $this->client->request('GET', '/openApi/cswap/v1/trade/forceOrders', $params);
    }

    /**
     * Get order details
     * 
     * GET /openApi/cswap/v1/trade/orderDetail
     * 
     * Returns detailed information about a specific order.
     * 
     * @param string $symbol Trading pair (e.g., 'BTC-USD')
     * @param string|null $orderId Order ID
     * @param string|null $clientOrderId Client order ID
     * @param int|null $recvWindow Request validity window in milliseconds
     * @return array Order details
     * 
     * @example
     * $order = $service->getOrderDetail('BTC-USD', orderId: '123456789');
     * $order = $service->getOrderDetail('BTC-USD', clientOrderId: 'my-order-1');
     */
    public function getOrderDetail(
        string $symbol,
        ?string $orderId = null,
        ?string $clientOrderId = null,
        ?int $recvWindow = null
    ): array {
        $params = ['symbol' => $symbol];
        
        if ($orderId) {
            $params['orderId'] = $orderId;
        }
        if ($clientOrderId) {
            $params['clientOrderId'] = $clientOrderId;
        }
        if ($recvWindow) {
            $params['recvWindow'] = $recvWindow;
        }
        
        return $this->client->request('GET', '/openApi/cswap/v1/trade/orderDetail', $params);
    }

    /**
     * Cancel an order
     * 
     * DELETE /openApi/cswap/v1/trade/order
     * 
     * Cancels a specific order.
     * 
     * @param string $symbol Trading pair (e.g., 'BTC-USD')
     * @param string|null $orderId Order ID
     * @param string|null $clientOrderId Client order ID
     * @param int|null $recvWindow Request validity window in milliseconds
     * @return array Cancellation result
     * 
     * @example
     * $result = $service->cancelOrder('BTC-USD', orderId: '123456789');
     * $result = $service->cancelOrder('BTC-USD', clientOrderId: 'my-order-1');
     */
    public function cancelOrder(
        string $symbol,
        ?string $orderId = null,
        ?string $clientOrderId = null,
        ?int $recvWindow = null
    ): array {
        $params = ['symbol' => $symbol];
        
        if ($orderId) {
            $params['orderId'] = $orderId;
        }
        if ($clientOrderId) {
            $params['clientOrderId'] = $clientOrderId;
        }
        if ($recvWindow) {
            $params['recvWindow'] = $recvWindow;
        }
        
        return $this->client->request('DELETE', '/openApi/cswap/v1/trade/order', $params);
    }

    /**
     * Get pending orders
     * 
     * GET /openApi/cswap/v1/trade/openOrders
     * 
     * Returns all open (pending) orders.
     * 
     * @param string|null $symbol Trading pair (e.g., 'BTC-USD'). If null, returns all symbols.
     * @param int|null $recvWindow Request validity window in milliseconds
     * @return array Open orders
     * 
     * @example
     * $openOrders = $service->getOpenOrders('BTC-USD');
     * $allOpenOrders = $service->getOpenOrders();
     */
    public function getOpenOrders(?string $symbol = null, ?int $recvWindow = null): array
    {
        $params = [];
        if ($symbol) {
            $params['symbol'] = $symbol;
        }
        if ($recvWindow) {
            $params['recvWindow'] = $recvWindow;
        }
        
        return $this->client->request('GET', '/openApi/cswap/v1/trade/openOrders', $params);
    }

    /**
     * Query order
     * 
     * GET /openApi/cswap/v1/trade/order
     * 
     * Returns information about a specific order.
     * 
     * @param string $symbol Trading pair (e.g., 'BTC-USD')
     * @param string|null $orderId Order ID
     * @param string|null $clientOrderId Client order ID
     * @param int|null $recvWindow Request validity window in milliseconds
     * @return array Order information
     * 
     * @example
     * $order = $service->getOrder('BTC-USD', orderId: '123456789');
     */
    public function getOrder(
        string $symbol,
        ?string $orderId = null,
        ?string $clientOrderId = null,
        ?int $recvWindow = null
    ): array {
        $params = ['symbol' => $symbol];
        
        if ($orderId) {
            $params['orderId'] = $orderId;
        }
        if ($clientOrderId) {
            $params['clientOrderId'] = $clientOrderId;
        }
        if ($recvWindow) {
            $params['recvWindow'] = $recvWindow;
        }
        
        return $this->client->request('GET', '/openApi/cswap/v1/trade/order', $params);
    }

    /**
     * Get order history
     * 
     * GET /openApi/cswap/v1/trade/historyOrders
     * 
     * Returns historical orders (filled, cancelled, rejected).
     * 
     * @param string $symbol Trading pair (e.g., 'BTC-USD')
     * @param int|null $startTime Start time in milliseconds
     * @param int|null $endTime End time in milliseconds
     * @param int $limit Number of records (default: 500, max: 1000)
     * @param int|null $recvWindow Request validity window in milliseconds
     * @return array Historical orders
     * 
     * @example
     * $history = $service->getHistoryOrders('BTC-USD', limit: 100);
     */
    public function getHistoryOrders(
        string $symbol,
        ?int $startTime = null,
        ?int $endTime = null,
        int $limit = 500,
        ?int $recvWindow = null
    ): array {
        $params = [
            'symbol' => $symbol,
            'limit' => $limit
        ];
        
        if ($startTime) {
            $params['startTime'] = $startTime;
        }
        if ($endTime) {
            $params['endTime'] = $endTime;
        }
        if ($recvWindow) {
            $params['recvWindow'] = $recvWindow;
        }
        
        return $this->client->request('GET', '/openApi/cswap/v1/trade/historyOrders', $params);
    }

    /**
     * Query margin type
     * 
     * GET /openApi/cswap/v1/trade/marginType
     * 
     * Returns the current margin type for a symbol.
     * 
     * @param string $symbol Trading pair (e.g., 'BTC-USD')
     * @param int|null $recvWindow Request validity window in milliseconds
     * @return array Margin type data: 'ISOLATED' or 'CROSSED'
     * 
     * @example
     * $marginType = $service->getMarginType('BTC-USD');
     * echo "Margin type: {$marginType['marginType']}";
     */
    public function getMarginType(string $symbol, ?int $recvWindow = null): array
    {
        $params = ['symbol' => $symbol];
        if ($recvWindow) {
            $params['recvWindow'] = $recvWindow;
        }
        
        return $this->client->request('GET', '/openApi/cswap/v1/trade/marginType', $params);
    }

    /**
     * Set margin type
     * 
     * POST /openApi/cswap/v1/trade/marginType
     * 
     * Changes the margin type for a symbol.
     * 
     * @param string $symbol Trading pair (e.g., 'BTC-USD')
     * @param string $marginType Margin type: 'ISOLATED' or 'CROSSED'
     * @param int|null $recvWindow Request validity window in milliseconds
     * @return array Updated margin type data
     * 
     * @example
     * $result = $service->setMarginType('BTC-USD', 'ISOLATED');
     */
    public function setMarginType(string $symbol, string $marginType, ?int $recvWindow = null): array
    {
        $params = [
            'symbol' => $symbol,
            'marginType' => $marginType
        ];
        
        if ($recvWindow) {
            $params['recvWindow'] = $recvWindow;
        }
        
        return $this->client->request('POST', '/openApi/cswap/v1/trade/marginType', $params);
    }

    /**
     * Adjust isolated margin
     * 
     * POST /openApi/cswap/v1/trade/margin
     * 
     * Adds or removes margin for an isolated position.
     * 
     * @param string $symbol Trading pair (e.g., 'BTC-USD')
     * @param string $positionSide Position side: 'LONG' or 'SHORT'
     * @param float $amount Amount to add or remove
     * @param int $type Operation type: 1 = add margin, 2 = remove margin
     * @param int|null $recvWindow Request validity window in milliseconds
     * @return array Margin adjustment result
     * 
     * @example
     * // Add 0.1 BTC margin to long position
     * $result = $service->adjustMargin('BTC-USD', 'LONG', 0.1, 1);
     * 
     * // Remove 0.05 BTC margin from short position
     * $result = $service->adjustMargin('BTC-USD', 'SHORT', 0.05, 2);
     */
    public function adjustMargin(
        string $symbol,
        string $positionSide,
        float $amount,
        int $type,
        ?int $recvWindow = null
    ): array {
        $params = [
            'symbol' => $symbol,
            'positionSide' => $positionSide,
            'amount' => $amount,
            'type' => $type
        ];
        
        if ($recvWindow) {
            $params['recvWindow'] = $recvWindow;
        }
        
        return $this->client->request('POST', '/openApi/cswap/v1/trade/margin', $params);
    }
}
