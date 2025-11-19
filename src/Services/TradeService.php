<?php

namespace Tigusigalpa\BingX\Services;

use Tigusigalpa\BingX\Http\BaseHttpClient;
use Tigusigalpa\BingX\Builder\OrderBuilder;

class TradeService
{
    protected BaseHttpClient $client;

    /**
     * BingX standard futures trading commission rate (0.045%)
     */
    public const FUTURES_COMMISSION_RATE = 0.00045; // 0.045% = 0.00045

    public function __construct(BaseHttpClient $client)
    {
        $this->client = $client;
    }

    /**
     * Calculate futures trading commission
     * 
     * @param float $margin Margin amount in USDT
     * @param int $leverage Leverage multiplier
     * @param float|null $commissionRate Custom commission rate (default: 0.045%)
     * @return array Commission details
     */
    public function calculateFuturesCommission(float $margin, int $leverage, ?float $commissionRate = null): array
    {
        $rate = $commissionRate ?? self::FUTURES_COMMISSION_RATE;
        $positionValue = $margin * $leverage;
        $commission = $positionValue * $rate;
        
        return [
            'margin' => $margin,
            'leverage' => $leverage,
            'position_value' => $positionValue,
            'commission_rate' => $rate,
            'commission_rate_percent' => $rate * 100,
            'commission' => $commission,
            'commission_rounded' => round($commission, 6),
            'net_position_value' => $positionValue - $commission
        ];
    }

    /**
     * Calculate commission for multiple orders
     * 
     * @param array $orders Array of ['margin' => float, 'leverage' => int]
     * @param float|null $commissionRate Custom commission rate
     * @return array Total commission and individual order details
     */
    public function calculateBatchCommission(array $orders, ?float $commissionRate = null): array
    {
        $rate = $commissionRate ?? self::FUTURES_COMMISSION_RATE;
        $totalCommission = 0;
        $orderDetails = [];
        
        foreach ($orders as $index => $order) {
            $commission = $this->calculateFuturesCommission(
                $order['margin'], 
                $order['leverage'], 
                $rate
            );
            
            $orderDetails[] = $commission;
            $totalCommission += $commission['commission'];
        }
        
        return [
            'commission_rate' => $rate,
            'commission_rate_percent' => $rate * 100,
            'total_commission' => $totalCommission,
            'total_commission_rounded' => round($totalCommission, 6),
            'orders_count' => count($orders),
            'orders' => $orderDetails
        ];
    }

    /**
     * Get commission rate constants
     * 
     * @return array Available commission rates
     */
    public function getCommissionRates(): array
    {
        return [
            'futures_standard' => [
                'rate' => self::FUTURES_COMMISSION_RATE,
                'percent' => self::FUTURES_COMMISSION_RATE * 100,
                'description' => 'Standard futures trading commission'
            ],
            'futures_standard_formula' => 'margin × leverage × 0.045%'
        ];
    }

    /**
     * Quick commission calculation helper
     * 
     * @param float $margin Margin amount
     * @param int $leverage Leverage
     * @return float Commission amount
     */
    public function getCommissionAmount(float $margin, int $leverage): float
    {
        return $margin * $leverage * self::FUTURES_COMMISSION_RATE;
    }

    /**
     * Create a new order builder for advanced order creation
     * 
     * @return OrderBuilder
     */
    public function order(): OrderBuilder
    {
        return new OrderBuilder($this);
    }

    /**
     * Quick spot market buy order
     * 
     * @param string $symbol Trading symbol (e.g., "BTC-USDT")
     * @param float $quantity Order quantity
     * @return array
     */
    public function spotMarketBuy(string $symbol, float $quantity): array
    {
        return $this->order()
            ->spot()
            ->symbol($symbol)
            ->buy()
            ->type('MARKET')
            ->quantity($quantity)
            ->execute();
    }

    /**
     * Quick spot market sell order
     * 
     * @param string $symbol Trading symbol (e.g., "BTC-USDT")
     * @param float $quantity Order quantity
     * @return array
     */
    public function spotMarketSell(string $symbol, float $quantity): array
    {
        return $this->order()
            ->spot()
            ->symbol($symbol)
            ->sell()
            ->type('MARKET')
            ->quantity($quantity)
            ->execute();
    }

    /**
     * Quick spot limit buy order
     * 
     * @param string $symbol Trading symbol
     * @param float $quantity Order quantity
     * @param float $price Order price
     * @return array
     */
    public function spotLimitBuy(string $symbol, float $quantity, float $price): array
    {
        return $this->order()
            ->spot()
            ->symbol($symbol)
            ->buy()
            ->type('LIMIT')
            ->quantity($quantity)
            ->price($price)
            ->execute();
    }

    /**
     * Quick spot limit sell order
     * 
     * @param string $symbol Trading symbol
     * @param float $quantity Order quantity
     * @param float $price Order price
     * @return array
     */
    public function spotLimitSell(string $symbol, float $quantity, float $price): array
    {
        return $this->order()
            ->spot()
            ->symbol($symbol)
            ->sell()
            ->type('LIMIT')
            ->quantity($quantity)
            ->price($price)
            ->execute();
    }

    /**
     * Quick futures long market order
     * 
     * @param string $symbol Trading symbol
     * @param float $margin Margin amount
     * @param int $leverage Leverage (default: 10)
     * @return array
     */
    public function futuresLongMarket(string $symbol, float $margin, int $leverage = 10): array
    {
        return $this->order()
            ->futures()
            ->symbol($symbol)
            ->buy()
            ->long()
            ->type('MARKET')
            ->margin($margin)
            ->leverage($leverage)
            ->execute();
    }

    /**
     * Quick futures short market order
     * 
     * @param string $symbol Trading symbol
     * @param float $margin Margin amount
     * @param int $leverage Leverage (default: 10)
     * @return array
     */
    public function futuresShortMarket(string $symbol, float $margin, int $leverage = 10): array
    {
        return $this->order()
            ->futures()
            ->symbol($symbol)
            ->sell()
            ->short()
            ->type('MARKET')
            ->margin($margin)
            ->leverage($leverage)
            ->execute();
    }

    /**
     * Quick futures long limit order with stop loss and take profit
     * 
     * @param string $symbol Trading symbol
     * @param float $margin Margin amount
     * @param float $price Entry price
     * @param float|null $stopLoss Stop loss price (optional)
     * @param float|null $takeProfit Take profit price (optional)
     * @param int $leverage Leverage (default: 10)
     * @return array
     */
    public function futuresLongLimit(
        string $symbol, 
        float $margin, 
        float $price, 
        ?float $stopLoss = null, 
        ?float $takeProfit = null, 
        int $leverage = 10
    ): array {
        $builder = $this->order()
            ->futures()
            ->symbol($symbol)
            ->buy()
            ->long()
            ->type('LIMIT')
            ->margin($margin)
            ->price($price)
            ->leverage($leverage);

        if ($stopLoss !== null) {
            $builder->stopLoss($stopLoss);
        }
        if ($takeProfit !== null) {
            $builder->takeProfit($takeProfit);
        }

        return $builder->execute();
    }

    /**
     * Quick futures short limit order with stop loss and take profit
     * 
     * @param string $symbol Trading symbol
     * @param float $margin Margin amount
     * @param float $price Entry price
     * @param float|null $stopLoss Stop loss price (optional)
     * @param float|null $takeProfit Take profit price (optional)
     * @param int $leverage Leverage (default: 10)
     * @return array
     */
    public function futuresShortLimit(
        string $symbol, 
        float $margin, 
        float $price, 
        ?float $stopLoss = null, 
        ?float $takeProfit = null, 
        int $leverage = 10
    ): array {
        $builder = $this->order()
            ->futures()
            ->symbol($symbol)
            ->sell()
            ->short()
            ->type('LIMIT')
            ->margin($margin)
            ->price($price)
            ->leverage($leverage);

        if ($stopLoss !== null) {
            $builder->stopLoss($stopLoss);
        }
        if ($takeProfit !== null) {
            $builder->takeProfit($takeProfit);
        }

        return $builder->execute();
    }

    /**
     * Advanced futures order with percentage-based stop loss/take profit
     * 
     * @param string $symbol Trading symbol
     * @param string $side BUY or SELL
     * @param string $positionSide LONG or SHORT
     * @param float $margin Margin amount
     * @param float $price Entry price
     * @param float|null $stopLossPercent Stop loss percentage (e.g., 5 for 5%)
     * @param float|null $takeProfitPercent Take profit percentage (e.g., 10 for 10%)
     * @param int $leverage Leverage (default: 10)
     * @return array
     */
    public function futuresOrderWithPercentages(
        string $symbol,
        string $side,
        string $positionSide,
        float $margin,
        float $price,
        ?float $stopLossPercent = null,
        ?float $takeProfitPercent = null,
        int $leverage = 10
    ): array {
        $builder = $this->order()
            ->futures()
            ->symbol($symbol)
            ->type('LIMIT')
            ->margin($margin)
            ->price($price)
            ->leverage($leverage);

        if ($side === 'BUY') {
            $builder->buy();
        } else {
            $builder->sell();
        }

        if ($positionSide === 'LONG') {
            $builder->long();
        } else {
            $builder->short();
        }

        if ($stopLossPercent !== null) {
            $builder->stopLossPercent($stopLossPercent);
        }
        if ($takeProfitPercent !== null) {
            $builder->takeProfitPercent($takeProfitPercent);
        }

        return $builder->execute();
    }

    /**
     * Create a new order.
     *
     * Обертка над эндпоинтом POST /openApi/swap/v2/trade/order.
     * Метод принимает массив параметров и пробрасывает их в BingX API
     * без дополнительной фильтрации, поэтому можно использовать все поля
     * из официальной документации.
     *
     * Наиболее часто используемые параметры:
     * - symbol: string (required)  Торговый символ, например "BTC-USDT".
     * - side: string (required)    Сторона ордера (BUY, SELL).
     * - type: string (required)    Тип ордера (MARKET, LIMIT, STOP, STOP_MARKET, TAKE_PROFIT и т.п.).
     * - quantity: float            Количество (для QTY‑ордера).
     * - margin: float              Маржа (для маржинальных/фьючерсных ордеров, если требуется).
     * - price: float               Цена ордера (обязательно для LIMIT/STOP‑типов).
     * - priceRate: float           Коэффициент цены для условных/трейлинг‑ордера.
     * - positionSide: string       Сторона позиции (LONG, SHORT, BOTH).
     * - timeInForce: string        GTC, IOC, FOK и т.п.
     * - clientOrderId: string      Пользовательский ID ордера.
     * - reduceOnly: bool           Закрытие только части/всей позиции без увеличения.
     * - closePosition: bool        Закрыть всю позицию.
     * - stopLoss: float            Цена стоп‑лосса (защитный ордер).
     * - takeProfit: float          Цена тейк‑профита.
     * - stopPrice: float           Триггер‑цена для STOP / TAKE_PROFIT ордеров.
     * - stopGuaranteed: bool       Гарантированный стоп (см. ограничения в документации BingX).
     * - positionId: int            Идентификатор позиции, если требуется привязка к существующей позиции.
     * - workingType: string        Тип цены для триггера (MARK_PRICE, LAST_PRICE и т.п.).
     * - newOrderRespType: string   Формат ответа (ACK, RESULT, FULL).
     * - timestamp: int             Временная метка запроса в миллисекундах.
     * - recvWindow: int            Допустимое окно времени запроса в миллисекундах.
     *
     * @param array $params Ассоциативный массив параметров ордера согласно документации BingX.
     * @return array Ответ BingX API.
     */
    public function createOrder(array $params): array
    {
        return $this->client->request('POST', '/openApi/swap/v2/trade/order', $params);
    }

    /**
     * Create a test order (won't execute in real market)
     * 
     * @param array $params Order parameters (same as createOrder)
     * @return array Test order response
     */
    public function createTestOrder(array $params): array
    {
        return $this->client->request('POST', '/openApi/swap/v2/trade/order/test', $params);
    }

    /**
     * Create batch orders
     * 
     * @param array $orders Array of order parameters
     * @return array
     */
    public function createBatchOrders(array $orders): array
    {
        return $this->client->request('POST', '/openApi/swap/v2/trade/batchOrders', [
            'orders' => json_encode($orders)
        ]);
    }

    /**
     * Cancel an existing order
     * 
     * @param string $symbol Trading symbol
     * @param string $orderId Order ID to cancel
     * @return array
     */
    public function cancelOrder(
        string $symbol,
        ?string $orderId = null,
        ?string $clientOrderId = null,
        ?int $timestamp = null,
        ?int $recvWindow = null
    ): array {
        $params = [
            'symbol'    => $symbol,
            'timestamp' => $timestamp ?? (int) (microtime(true) * 1000),
        ];

        // В соответствии с документацией можно передавать либо orderId, либо clientOrderId
        if ($orderId !== null) {
            $params['orderId'] = $orderId;
        }

        if ($clientOrderId !== null) {
            $params['clientOrderId'] = $clientOrderId;
        }

        if ($recvWindow !== null) {
            $params['recvWindow'] = $recvWindow;
        }

        return $this->client->request('DELETE', '/openApi/swap/v2/trade/order', $params);
    }

    /**
     * Cancel all open orders for the current account.
     *
     * Обертка над эндпоинтом DELETE /openApi/swap/v2/trade/allOpenOrders.
     * Все параметры, кроме timestamp, являются опциональными и добавляются
     * в запрос только если переданы.
     *
     * Примеры использования:
     * - cancelAllOrders()                       // отменить все открытые ордера
     * - cancelAllOrders(null, 'BTC-USDT')       // отменить все ордера по символу
     * - cancelAllOrders(null, null, 'LIMIT')    // отменить все лимитные ордера
     *
     * @param int|null    $timestamp  Пользовательский timestamp в мс;
     *                                если не указан, будет сгенерирован автоматически.
     * @param string|null $symbol     Торговый символ (например, "BTC-USDT"), опционально.
     * @param string|null $type       Тип ордера (LIMIT, MARKET, LIMIT_MAKER и т.п.), опционально.
     * @param int|null    $recvWindow Допустимое окно времени запроса в мс, опционально.
     * @return array                  Ответ BingX API.
     */
    public function cancelAllOrders(
        ?int $timestamp = null,
        ?string $symbol = null,
        ?string $type = null,
        ?int $recvWindow = null
    ): array {
        $params = [
            'timestamp' => $timestamp ?? (int) (microtime(true) * 1000),
        ];

        if ($symbol !== null) {
            $params['symbol'] = $symbol;
        }

        if ($type !== null) {
            $params['type'] = $type;
        }

        if ($recvWindow !== null) {
            $params['recvWindow'] = $recvWindow;
        }

        return $this->client->request('DELETE', '/openApi/swap/v2/trade/allOpenOrders', $params);
    }

    /**
     * Cancel batch orders
     * 
     * @param string $symbol Trading symbol
     * @param array $orderIds Array of order IDs
     * @return array
     */
    public function cancelBatchOrders(
        string $symbol,
        array $orderIds,
        ?array $clientOrderIds = null,
        ?int $timestamp = null,
        ?int $recvWindow = null
    ): array {
        $params = [
            'symbol'    => $symbol,
            // API ожидает список ID через запятую
            'orderIds'  => implode(',', $orderIds),
            'timestamp' => $timestamp ?? (int) (microtime(true) * 1000),
        ];

        if ($clientOrderIds !== null && count($clientOrderIds) > 0) {
            $params['clientOrderIds'] = implode(',', $clientOrderIds);
        }

        if ($recvWindow !== null) {
            $params['recvWindow'] = $recvWindow;
        }

        return $this->client->request('DELETE', '/openApi/swap/v2/trade/batchOrders', $params);
    }

    /**
     * Get order details
     * 
     * @param string $symbol Trading symbol
     * @param string $orderId Order ID
     * @return array
     */
    public function getOrder(string $symbol, string $orderId): array
    {
        return $this->client->request('GET', '/openApi/swap/v2/trade/order', [
            'symbol' => $symbol,
            'orderId' => $orderId
        ]);
    }

    /**
     * Get open orders
     * 
     * @param string|null $symbol Trading symbol (optional)
     * @param int $limit Number of records (1-100)
     * @return array
     */
    public function getOpenOrders(?string $symbol = null, int $limit = 100): array
    {
        $params = ['limit' => $limit];
        if ($symbol) $params['symbol'] = $symbol;

        return $this->client->request('GET', '/openApi/swap/v2/trade/openOrders', $params);
    }

    /**
     * Get order history
     * 
     * @param string|null $symbol Trading symbol (optional)
     * @param int $limit Number of records (1-500)
     * @param int|null $startTime Start timestamp in milliseconds
     * @param int|null $endTime End timestamp in milliseconds
     * @return array
     */
    public function getOrderHistory(?string $symbol = null, int $limit = 100, ?int $startTime = null, ?int $endTime = null): array
    {
        $params = ['limit' => $limit];
        
        if ($symbol) $params['symbol'] = $symbol;
        if ($startTime) $params['startTime'] = $startTime;
        if ($endTime) $params['endTime'] = $endTime;

        return $this->client->request('GET', '/openApi/swap/v2/trade/orderHistory', $params);
    }

    /**
     * Get filled order history
     * 
     * @param string|null $symbol Trading symbol (optional)
     * @param int $limit Number of records (1-500)
     * @param int|null $startTime Start timestamp in milliseconds
     * @param int|null $endTime End timestamp in milliseconds
     * @return array
     */
    public function getFilledOrders(?string $symbol = null, int $limit = 100, ?int $startTime = null, ?int $endTime = null): array
    {
        $params = ['limit' => $limit];
        
        if ($symbol) $params['symbol'] = $symbol;
        if ($startTime) $params['startTime'] = $startTime;
        if ($endTime) $params['endTime'] = $endTime;

        return $this->client->request('GET', '/openApi/swap/v2/trade/filledOrders', $params);
    }

    /**
     * Get user trades
     * 
     * @param string|null $symbol Trading symbol (optional)
     * @param int $limit Number of records (1-500)
     * @param int|null $startTime Start timestamp in milliseconds
     * @param int|null $endTime End timestamp in milliseconds
     * @return array
     */
    public function getUserTrades(?string $symbol = null, int $limit = 100, ?int $startTime = null, ?int $endTime = null): array
    {
        $params = ['limit' => $limit];
        
        if ($symbol) $params['symbol'] = $symbol;
        if ($startTime) $params['startTime'] = $startTime;
        if ($endTime) $params['endTime'] = $endTime;

        return $this->client->request('GET', '/openApi/swap/v2/trade/userTrades', $params);
    }

    /**
     * Change leverage (Set Leverage)
     *
     * According to BingX swap V2 trade API docs, required parameters are:
     * - symbol: trading pair symbol with hyphen (e.g. BTC-USDT)
     * - side: leverage side for positions (LONG, SHORT, BOTH)
     * - leverage: leverage value
     * - timestamp: request timestamp in milliseconds
     * - recvWindow (optional): request valid time window in ms
     *
     * @param string $symbol Trading symbol (e.g., "BTC-USDT")
     * @param string $side Leverage side (LONG, SHORT, BOTH)
     * @param int $leverage Leverage value
     * @param int|null $recvWindow Optional recvWindow in milliseconds
     * @return array
     */
    public function changeLeverage(string $symbol, string $side, int $leverage, ?int $recvWindow = null): array
    {
        $params = [
            'symbol'    => $symbol,
            'side'      => $side,
            'leverage'  => $leverage,
            'timestamp' => (int) (microtime(true) * 1000),
        ];

        if ($recvWindow !== null) {
            $params['recvWindow'] = $recvWindow;
        }

        return $this->client->request('POST', '/openApi/swap/v2/trade/leverage', $params);
    }

    /**
     * Change margin type
     * 
     * @param string $symbol Trading symbol
     * @param string $marginType Margin type (ISOLATED, CROSSED)
     * @return array
     */
    public function changeMarginType(string $symbol, string $marginType): array
    {
        return $this->client->request('POST', '/openApi/swap/v2/trade/changeMarginType', [
            'symbol' => $symbol,
            'marginType' => $marginType
        ]);
    }

    /**
     * Modify position margin
     * 
     * @param string $symbol Trading symbol
     * @param string $positionSide Position side (LONG, SHORT, BOTH)
     * @param float $amount Margin amount
     * @param int $type Type (1: ADD, 2: REDUCE)
     * @return array
     */
    public function modifyPositionMargin(string $symbol, string $positionSide, float $amount, int $type): array
    {
        return $this->client->request('POST', '/openApi/swap/v2/trade/modifyPositionMargin', [
            'symbol' => $symbol,
            'positionSide' => $positionSide,
            'amount' => $amount,
            'type' => $type
        ]);
    }

    /**
     * Get historical trades (market trades)
     * 
     * @param string $symbol Trading symbol
     * @param int $limit Number of records (1-1000)
     * @param int|null $fromId Trade ID to fetch from
     * @param int|null $startTime Start timestamp in milliseconds
     * @param int|null $endTime End timestamp in milliseconds
     * @return array
     */
    public function getHistoricalTrades(string $symbol, int $limit = 500, ?int $fromId = null, ?int $startTime = null, ?int $endTime = null): array
    {
        $params = [
            'symbol' => $symbol,
            'limit' => $limit
        ];

        if ($fromId) $params['fromId'] = $fromId;
        if ($startTime) $params['startTime'] = $startTime;
        if ($endTime) $params['endTime'] = $endTime;

        return $this->client->request('GET', '/openApi/swap/v2/trade/historicalTrades', $params);
    }

    /**
     * Get commission rates for symbols from API
     * 
     * @param string|null $symbol Trading symbol (optional)
     * @return array
     */
    public function getApiCommissionRates(?string $symbol = null): array
    {
        $params = [];
        if ($symbol) $params['symbol'] = $symbol;

        return $this->client->request('GET', '/openApi/swap/v2/trade/commissionRates', $params);
    }

    /**
     * Get account trades (recent trades list)
     * 
     * @param string $symbol Trading symbol
     * @param int $limit Number of records (1-1000)
     * @param int|null $fromId Trade ID to fetch from
     * @param int|null $startTime Start timestamp in milliseconds
     * @param int|null $endTime End timestamp in milliseconds
     * @return array
     */
    public function getAccountTrades(string $symbol, int $limit = 500, ?int $fromId = null, ?int $startTime = null, ?int $endTime = null): array
    {
        $params = [
            'symbol' => $symbol,
            'limit' => $limit
        ];

        if ($fromId) $params['fromId'] = $fromId;
        if ($startTime) $params['startTime'] = $startTime;
        if ($endTime) $params['endTime'] = $endTime;

        return $this->client->request('GET', '/openApi/swap/v2/trade/accountTrades', $params);
    }

    /**
     * Get position mode
     * 
     * @return array Position mode information
     */
    public function getPositionMode(): array
    {
        return $this->client->request('GET', '/openApi/swap/v2/trade/positionMode');
    }

    /**
     * Set position mode
     * 
     * @param string $mode Position mode (HEDGE_MODE, ONE_WAY_MODE)
     * @return array
     */
    public function setPositionMode(string $mode): array
    {
        return $this->client->request('POST', '/openApi/swap/v2/trade/positionMode', [
            'mode' => $mode
        ]);
    }

    /**
     * Get position side
     * 
     * @return array Position side information
     */
    public function getPositionSide(): array
    {
        return $this->client->request('GET', '/openApi/swap/v2/trade/positionSide');
    }

    /**
     * Set position side
     * 
     * @param string $positionSide Position side (BOTH, LONG, SHORT)
     * @return array
     */
    public function setPositionSide(string $positionSide): array
    {
        return $this->client->request('POST', '/openApi/swap/v2/trade/positionSide', [
            'positionSide' => $positionSide
        ]);
    }

    /**
     * Get order modification history
     * 
     * @param string $symbol Trading symbol
     * @param int $limit Number of records (1-500)
     * @param int|null $startTime Start timestamp in milliseconds
     * @param int|null $endTime End timestamp in milliseconds
     * @return array
     */
    public function getOrderModificationHistory(string $symbol, int $limit = 100, ?int $startTime = null, ?int $endTime = null): array
    {
        $params = [
            'symbol' => $symbol,
            'limit' => $limit
        ];

        if ($startTime) $params['startTime'] = $startTime;
        if ($endTime) $params['endTime'] = $endTime;

        return $this->client->request('GET', '/openApi/swap/v2/trade/orderModificationHistory', $params);
    }

    /**
     * Get leverage bracket
     * 
     * @param string|null $symbol Trading symbol (optional)
     * @return array
     */
    public function getLeverageBracket(?string $symbol = null): array
    {
        $params = [];
        if ($symbol) $params['symbol'] = $symbol;

        return $this->client->request('GET', '/openApi/swap/v2/trade/leverageBracket', $params);
    }

    /**
     * Get adl quantile
     * 
     * @param string $symbol Trading symbol
     * @return array
     */
    public function getAdlQuantile(string $symbol): array
    {
        return $this->client->request('GET', '/openApi/swap/v2/trade/adlQuantile', [
            'symbol' => $symbol
        ]);
    }

    /**
     * Get force orders
     * 
     * @param string $symbol Trading symbol
     * @param int $limit Number of records (1-100)
     * @param int|null $startTime Start timestamp in milliseconds
     * @param int|null $endTime End timestamp in milliseconds
     * @return array
     */
    public function getForceOrders(string $symbol, int $limit = 100, ?int $startTime = null, ?int $endTime = null): array
    {
        $params = [
            'symbol' => $symbol,
            'limit' => $limit
        ];

        if ($startTime) $params['startTime'] = $startTime;
        if ($endTime) $params['endTime'] = $endTime;

        return $this->client->request('GET', '/openApi/swap/v2/trade/forceOrders', $params);
    }

    /**
     * Get open interest statistics
     * 
     * @param string $symbol Trading symbol
     * @param int $limit Number of records (1-500)
     * @param int|null $startTime Start timestamp in milliseconds
     * @param int|null $endTime End timestamp in milliseconds
     * @return array
     */
    public function getOpenInterestStatistics(string $symbol, int $limit = 500, ?int $startTime = null, ?int $endTime = null): array
    {
        $params = [
            'symbol' => $symbol,
            'limit' => $limit
        ];

        if ($startTime) $params['startTime'] = $startTime;
        if ($endTime) $params['endTime'] = $endTime;

        return $this->client->request('GET', '/openApi/swap/v2/trade/openInterestStatistics', $params);
    }

    /**
     * Get open interest
     * 
     * @param string $symbol Trading symbol
     * @return array
     */
    public function getOpenInterest(string $symbol): array
    {
        return $this->client->request('GET', '/openApi/swap/v2/trade/openInterest', [
            'symbol' => $symbol
        ]);
    }

    /**
     * Get open interest history
     * 
     * @param string $symbol Trading symbol
     * @param int $limit Number of records (1-500)
     * @param int|null $startTime Start timestamp in milliseconds
     * @param int|null $endTime End timestamp in milliseconds
     * @return array
     */
    public function getOpenInterestHistory(string $symbol, int $limit = 500, ?int $startTime = null, ?int $endTime = null): array
    {
        $params = [
            'symbol' => $symbol,
            'limit' => $limit
        ];

        if ($startTime) $params['startTime'] = $startTime;
        if ($endTime) $params['endTime'] = $endTime;

        return $this->client->request('GET', '/openApi/swap/v2/trade/openInterestHistory', $params);
    }

    /**
     * Get average price
     * 
     * @param string $symbol Trading symbol
     * @param string $side Order side (BUY, SELL)
     * @param string $type Order type (MARKET, LIMIT)
     * @param float|null $quantity Order quantity
     * @param float|null $price Order price (required for LIMIT orders)
     * @return array
     */
    public function getAvgPrice(string $symbol, string $side, string $type, ?float $quantity = null, ?float $price = null): array
    {
        $params = [
            'symbol' => $symbol,
            'side' => $side,
            'type' => $type
        ];

        if ($quantity !== null) $params['quantity'] = $quantity;
        if ($price !== null) $params['price'] = $price;

        return $this->client->request('GET', '/openApi/swap/v2/trade/avgPrice', $params);
    }

    /**
     * Get recent trades list
     * 
     * @param string $symbol Trading symbol
     * @param int $limit Number of records (1-1000)
     * @param int|null $fromId Trade ID to fetch from
     * @return array
     */
    public function getRecentTradesList(string $symbol, int $limit = 500, ?int $fromId = null): array
    {
        $params = [
            'symbol' => $symbol,
            'limit' => $limit
        ];

        if ($fromId !== null) $params['fromId'] = $fromId;

        return $this->client->request('GET', '/openApi/swap/v2/trade/recentTradesList', $params);
    }

    /**
     * Get max open orders count
     * 
     * @return array
     */
    public function getMaxOpenOrders(): array
    {
        return $this->client->request('GET', '/openApi/swap/v2/trade/maxOpenOrders');
    }

    /**
     * Set max open orders count
     * 
     * @param int $maxOpenOrders Maximum number of open orders
     * @return array
     */
    public function setMaxOpenOrders(int $maxOpenOrders): array
    {
        return $this->client->request('POST', '/openApi/swap/v2/trade/maxOpenOrders', [
            'maxOpenOrders' => $maxOpenOrders
        ]);
    }

    /**
     * Get notional and leverage brackets
     * 
     * @param string|null $symbol Trading symbol (optional)
     * @return array
     */
    public function getNotionalAndLeverageBrackets(?string $symbol = null): array
    {
        $params = [];
        if ($symbol) $params['symbol'] = $symbol;

        return $this->client->request('GET', '/openApi/swap/v2/trade/notionalAndLeverageBracket', $params);
    }

    /**
     * Get income history
     * 
     * @param string $symbol Trading symbol (optional)
     * @param string $incomeType Income type (COMMISSION, FUNDING_FEE, etc.)
 * @param int $limit Number of records (1-1000)
     * @param int|null $startTime Start timestamp in milliseconds
     * @param int|null $endTime End timestamp in milliseconds
     * @return array
     */
    public function getIncomeHistory(?string $symbol = null, string $incomeType = 'ALL', int $limit = 100, ?int $startTime = null, ?int $endTime = null): array
    {
        $params = [
            'incomeType' => $incomeType,
            'limit' => $limit
        ];

        if ($symbol) $params['symbol'] = $symbol;
        if ($startTime) $params['startTime'] = $startTime;
        if ($endTime) $params['endTime'] = $endTime;

        return $this->client->request('GET', '/openApi/swap/v2/trade/income', $params);
    }

    /**
     * Get leverage bracket for symbol
     * 
     * @param string $symbol Trading symbol
     * @return array
     */
    public function getLeverageBracketForSymbol(string $symbol): array
    {
        return $this->client->request('GET', '/openApi/swap/v2/trade/leverageBracket', [
            'symbol' => $symbol
        ]);
    }

    /**
     * Cancel and replace order
     * 
     * @param string $symbol Trading symbol
     * @param string $cancelOrderId Order ID to cancel
     * @param string $side Order side (BUY, SELL)
     * @param string $type Order type (MARKET, LIMIT)
     * @param float|null $quantity Order quantity
     * @param float|null $price Order price (required for LIMIT orders)
     * @return array
     */
    public function cancelAndReplaceOrder(string $symbol, string $cancelOrderId, string $side, string $type, ?float $quantity = null, ?float $price = null): array
    {
        $params = [
            'symbol' => $symbol,
            'cancelOrderId' => $cancelOrderId,
            'side' => $side,
            'type' => $type
        ];

        if ($quantity !== null) $params['quantity'] = $quantity;
        if ($price !== null) $params['price'] = $price;

        return $this->client->request('POST', '/openApi/swap/v2/trade/cancelReplace', $params);
    }
}
