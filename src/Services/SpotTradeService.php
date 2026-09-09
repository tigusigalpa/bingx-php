<?php

namespace Tigusigalpa\BingX\Services;

use Tigusigalpa\BingX\Http\BaseHttpClient;

/**
 * BingX Spot Trading API.
 *
 * This service is intentionally separate from TradeService: Spot and
 * perpetual orders use different endpoints and different order semantics.
 */
class SpotTradeService
{
    public const ORDER_TYPE_LIMIT = 'LIMIT';
    public const ORDER_TYPE_MARKET = 'MARKET';
    public const SIDE_BUY = 'BUY';
    public const SIDE_SELL = 'SELL';
    public const TIME_IN_FORCE_GTC = 'GTC';
    public const TIME_IN_FORCE_IOC = 'IOC';
    public const TIME_IN_FORCE_FOK = 'FOK';
    public const TIME_IN_FORCE_POST_ONLY = 'PostOnly';
    public const CANCEL_REPLACE_STOP_ON_FAILURE = 'STOP_ON_FAILURE';
    public const CANCEL_REPLACE_ALLOW_FAILURE = 'ALLOW_FAILURE';

    protected BaseHttpClient $client;

    public function __construct(BaseHttpClient $client)
    {
        $this->client = $client;
    }

    /**
     * Place a spot order with raw BingX parameters. Use this for advanced
     * order types not represented by SpotOrderRequest.
     */
    public function createOrder(array $params): array
    {
        return $this->client->request('POST', '/openApi/spot/v1/trade/order', $params);
    }

    /**
     * Place a validated LIMIT or MARKET spot order.
     */
    public function createOrderRequest(SpotOrderRequest $order): array
    {
        $this->validateOrderRequest($order);

        return $this->createOrder($this->orderRequestToParams($order));
    }

    /**
     * Cancel one spot order by exchange or client ID.
     */
    public function cancelOrder(string $symbol, ?string $orderId = null, ?string $clientOrderId = null): array
    {
        $this->requireSymbol($symbol);
        $this->requireOrderIdentifier($orderId, $clientOrderId, 'cancelOrder');

        $params = ['symbol' => $symbol];
        if ($orderId !== null) {
            $params['orderId'] = $orderId;
        }
        if ($clientOrderId !== null) {
            $params['clientOrderID'] = $clientOrderId;
        }

        return $this->client->request('POST', '/openApi/spot/v1/trade/cancel', $params);
    }

    /**
     * Cancel all open spot orders, optionally limited to one symbol.
     */
    public function cancelAllOrders(?string $symbol = null): array
    {
        $params = [];
        if ($symbol !== null) {
            $this->requireSymbol($symbol);
            $params['symbol'] = $symbol;
        }

        return $this->client->request('POST', '/openApi/spot/v1/trade/cancelOpenOrders', $params);
    }

    /**
     * Cancel a batch of spot orders. At least one ID is required.
     */
    public function cancelBatchOrders(string $symbol, array $orderIds = [], array $clientOrderIds = []): array
    {
        $this->requireSymbol($symbol);
        if ($orderIds === [] && $clientOrderIds === []) {
            throw new \InvalidArgumentException('cancelBatchOrders requires at least one order ID or client order ID');
        }

        $params = ['symbol' => $symbol];
        if ($orderIds !== []) {
            $params['orderIds'] = implode(',', $orderIds);
        }
        if ($clientOrderIds !== []) {
            $params['clientOrderIDs'] = implode(',', $clientOrderIds);
        }

        return $this->client->request('POST', '/openApi/spot/v1/trade/cancelOrders', $params);
    }

    /**
     * Atomically cancel and replace a spot order.
     */
    public function amendOrder(
        string $symbol,
        ?string $cancelOrderId,
        ?string $cancelClientOrderId,
        string $cancelReplaceMode,
        SpotOrderRequest $newOrder
    ): array {
        $this->requireSymbol($symbol);
        $this->requireOrderIdentifier($cancelOrderId, $cancelClientOrderId, 'amendOrder');

        if (!in_array($cancelReplaceMode, [self::CANCEL_REPLACE_STOP_ON_FAILURE, self::CANCEL_REPLACE_ALLOW_FAILURE], true)) {
            throw new \InvalidArgumentException('cancelReplaceMode must be STOP_ON_FAILURE or ALLOW_FAILURE');
        }

        if ($newOrder->symbol === '') {
            $newOrder->symbol = $symbol;
        }
        if ($newOrder->symbol !== $symbol) {
            throw new \InvalidArgumentException('The replacement order symbol must match the cancelled order symbol');
        }
        $this->validateOrderRequest($newOrder);

        $params = $this->orderRequestToParams($newOrder);
        $params['cancelReplaceMode'] = $cancelReplaceMode;
        if ($cancelOrderId !== null) {
            $params['cancelOrderId'] = $cancelOrderId;
        }
        if ($cancelClientOrderId !== null) {
            $params['cancelClientOrderID'] = $cancelClientOrderId;
        }

        return $this->client->request('POST', '/openApi/spot/v1/trade/order/cancelReplace', $params);
    }

    /**
     * Get one spot order by exchange or client ID.
     */
    public function getOrder(string $symbol, ?string $orderId = null, ?string $clientOrderId = null): array
    {
        $this->requireSymbol($symbol);
        $this->requireOrderIdentifier($orderId, $clientOrderId, 'getOrder');

        $params = ['symbol' => $symbol];
        if ($orderId !== null) {
            $params['orderId'] = $orderId;
        }
        if ($clientOrderId !== null) {
            $params['clientOrderID'] = $clientOrderId;
        }

        return $this->client->request('GET', '/openApi/spot/v1/trade/query', $params);
    }

    public function getOpenOrders(?string $symbol = null): array
    {
        $params = [];
        if ($symbol !== null) {
            $this->requireSymbol($symbol);
            $params['symbol'] = $symbol;
        }

        return $this->client->request('GET', '/openApi/spot/v1/trade/openOrders', $params);
    }

    public function getOrderHistory(?string $symbol = null, int $limit = 100, ?int $startTime = null, ?int $endTime = null): array
    {
        if ($limit < 1 || $limit > 100) {
            throw new \InvalidArgumentException('Limit must be between 1 and 100');
        }

        $params = ['pageSize' => $limit];
        if ($symbol !== null) {
            $this->requireSymbol($symbol);
            $params['symbol'] = $symbol;
        }
        if ($startTime !== null) {
            $params['startTime'] = $startTime;
        }
        if ($endTime !== null) {
            $params['endTime'] = $endTime;
        }

        return $this->client->request('GET', '/openApi/spot/v1/trade/historyOrders', $params);
    }

    public function getTrades(?string $symbol = null, int $limit = 100, ?int $startTime = null, ?int $endTime = null): array
    {
        if ($limit < 1 || $limit > 100) {
            throw new \InvalidArgumentException('Limit must be between 1 and 100');
        }

        $params = ['pageSize' => $limit];
        if ($symbol !== null) {
            $this->requireSymbol($symbol);
            $params['symbol'] = $symbol;
        }
        if ($startTime !== null) {
            $params['startTime'] = $startTime;
        }
        if ($endTime !== null) {
            $params['endTime'] = $endTime;
        }

        return $this->client->request('GET', '/openApi/spot/v1/trade/myTrades', $params);
    }

    protected function orderRequestToParams(SpotOrderRequest $order): array
    {
        $params = [
            'symbol' => $order->symbol,
            'side' => $order->side,
            'type' => $order->type,
        ];

        if ($order->quantity !== '') {
            $params['quantity'] = $order->quantity;
        }
        if ($order->price !== null) {
            $params['price'] = $order->price;
        }
        if ($order->quoteOrderQty !== null) {
            $params['quoteOrderQty'] = $order->quoteOrderQty;
        }
        if ($order->timeInForce !== null) {
            $params['timeInForce'] = $order->timeInForce;
        }
        if ($order->clientOrderId !== null) {
            $params['newClientOrderId'] = $order->clientOrderId;
        }

        return $params;
    }

    protected function validateOrderRequest(SpotOrderRequest $order): void
    {
        $this->requireSymbol($order->symbol);
        if (!in_array($order->side, [self::SIDE_BUY, self::SIDE_SELL], true)) {
            throw new \InvalidArgumentException('Spot order side must be BUY or SELL');
        }
        if (!in_array($order->type, [self::ORDER_TYPE_LIMIT, self::ORDER_TYPE_MARKET], true)) {
            throw new \InvalidArgumentException('SpotOrderRequest supports LIMIT and MARKET orders only; use createOrder() for advanced types');
        }

        $hasQuantity = $this->isPositiveDecimal($order->quantity);
        $hasQuoteOrderQty = $order->quoteOrderQty !== null && $this->isPositiveDecimal($order->quoteOrderQty);

        if ($order->type === self::ORDER_TYPE_LIMIT) {
            if ($order->price === null || !$this->isPositiveDecimal($order->price)) {
                throw new \InvalidArgumentException('A LIMIT spot order requires a positive price');
            }
            if (!$hasQuantity) {
                throw new \InvalidArgumentException('A LIMIT spot order requires a positive quantity');
            }
        } elseif (!$hasQuantity && !$hasQuoteOrderQty) {
            throw new \InvalidArgumentException('A MARKET spot order requires a positive quantity or quoteOrderQty');
        }
    }

    protected function requireSymbol(string $symbol): void
    {
        if ($symbol === '') {
            throw new \InvalidArgumentException('Symbol is required');
        }
    }

    protected function requireOrderIdentifier(?string $orderId, ?string $clientOrderId, string $operation): void
    {
        if ($orderId === null && $clientOrderId === null) {
            throw new \InvalidArgumentException($operation . ' requires an order ID or client order ID');
        }
    }

    protected function isPositiveDecimal(string $value): bool
    {
        return is_numeric($value) && (float) $value > 0;
    }
}
