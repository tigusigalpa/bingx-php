<?php

namespace Tigusigalpa\BingX\Builder;

use Tigusigalpa\BingX\Services\TradeService;

class OrderBuilder
{
    protected TradeService $trade;
    protected array $orderData = [];
    protected string $orderType = 'futures'; // spot or futures
    protected bool $isValid = true;
    protected array $errors = [];
    protected bool $isTestOrder = false;

    public function __construct(TradeService $trade)
    {
        $this->trade = $trade;
        $this->orderData = [
            'timestamp' => (int)(microtime(true) * 1000)
        ];
    }

    /**
     * Set order as test order (won't execute in real market)
     */
    public function test(): self
    {
        $this->isTestOrder = true;
        return $this;
    }

    /**
     * Set order type to spot trading
     */
    public function spot(): self
    {
        $this->orderType = 'spot';
        return $this;
    }

    /**
     * Set order type to futures/margin trading
     */
    public function futures(): self
    {
        $this->orderType = 'futures';
        return $this;
    }

    /**
     * Set trading symbol
     */
    public function symbol(string $symbol): self
    {
        $this->orderData['symbol'] = $symbol;
        return $this;
    }

    /**
     * Set order type: MARKET, LIMIT, STOP, STOP_MARKET
     */
    public function type(string $type): self
    {
        $validTypes = ['MARKET', 'LIMIT', 'STOP', 'STOP_MARKET'];
        if (!in_array($type, $validTypes)) {
            $this->addError("Invalid order type: {$type}");
            return $this;
        }
        
        $this->orderData['type'] = $type;
        return $this;
    }

    /**
     * Set order side: BUY or SELL
     */
    public function buy(): self
    {
        $this->orderData['side'] = 'BUY';
        return $this;
    }

    /**
     * Set order side: BUY or SELL
     */
    public function sell(): self
    {
        $this->orderData['side'] = 'SELL';
        return $this;
    }

    /**
     * Set position side for futures: LONG or SHORT
     */
    public function long(): self
    {
        if ($this->orderType !== 'futures') {
            $this->addError("Position side (LONG/SHORT) only available for futures orders");
            return $this;
        }
        
        $this->orderData['positionSide'] = 'LONG';
        return $this;
    }

    /**
     * Set position side for futures: LONG or SHORT
     */
    public function short(): self
    {
        if ($this->orderType !== 'futures') {
            $this->addError("Position side (LONG/SHORT) only available for futures orders");
            return $this;
        }
        
        $this->orderData['positionSide'] = 'SHORT';
        return $this;
    }

    /**
     * Set leverage for futures trading
     */
    public function leverage(int $leverage): self
    {
        if ($this->orderType !== 'futures') {
            $this->addError("Leverage only available for futures orders");
            return $this;
        }
        
        if ($leverage < 1 || $leverage > 125) {
            $this->addError("Leverage must be between 1 and 125");
            return $this;
        }
        
        $this->orderData['leverage'] = $leverage;
        return $this;
    }

    /**
     * Set order quantity for spot trading
     */
    public function quantity(float $quantity): self
    {
        if ($this->orderType !== 'spot') {
            $this->addError("Quantity only available for spot orders. Use margin() for futures.");
            return $this;
        }
        
        if ($quantity <= 0) {
            $this->addError("Quantity must be greater than 0");
            return $this;
        }
        
        $this->orderData['quantity'] = $quantity;
        return $this;
    }

    /**
     * Set margin amount for futures trading
     */
    public function margin(float $margin): self
    {
        if ($this->orderType !== 'futures') {
            $this->addError("Margin only available for futures orders. Use quantity() for spot.");
            return $this;
        }
        
        if ($margin <= 0) {
            $this->addError("Margin must be greater than 0");
            return $this;
        }
        
        $this->orderData['margin'] = $margin;
        return $this;
    }

    /**
     * Set price for LIMIT orders
     */
    public function price(float $price): self
    {
        if (!isset($this->orderData['type']) || !in_array($this->orderData['type'], ['LIMIT', 'STOP'])) {
            $this->addError("Price only available for LIMIT or STOP orders");
            return $this;
        }
        
        if ($price <= 0) {
            $this->addError("Price must be greater than 0");
            return $this;
        }
        
        $this->orderData['price'] = $price;
        return $this;
    }

    /**
     * Set stop loss price (absolute value)
     */
    public function stopLoss(float $price): self
    {
        if ($this->orderType !== 'futures') {
            $this->addError("Stop loss only available for futures orders");
            return $this;
        }
        
        if ($price <= 0) {
            $this->addError("Stop loss price must be greater than 0");
            return $this;
        }
        
        $this->orderData['stopLoss'] = $price;
        return $this;
    }

    /**
     * Set stop loss percentage (e.g., 5 for 5%)
     */
    public function stopLossPercent(float $percent): self
    {
        if ($this->orderType !== 'futures') {
            $this->addError("Stop loss only available for futures orders");
            return $this;
        }
        
        if ($percent <= 0 || $percent > 100) {
            $this->addError("Stop loss percentage must be between 0 and 100");
            return $this;
        }
        
        $this->orderData['stopLossPercent'] = $percent;
        return $this;
    }

    /**
     * Set take profit price (absolute value)
     */
    public function takeProfit(float $price): self
    {
        if ($this->orderType !== 'futures') {
            $this->addError("Take profit only available for futures orders");
            return $this;
        }
        
        if ($price <= 0) {
            $this->addError("Take profit price must be greater than 0");
            return $this;
        }
        
        $this->orderData['takeProfit'] = $price;
        return $this;
    }

    /**
     * Set take profit percentage (e.g., 10 for 10%)
     */
    public function takeProfitPercent(float $percent): self
    {
        if ($this->orderType !== 'futures') {
            $this->addError("Take profit only available for futures orders");
            return $this;
        }
        
        if ($percent <= 0 || $percent > 1000) {
            $this->addError("Take profit percentage must be between 0 and 1000");
            return $this;
        }
        
        $this->orderData['takeProfitPercent'] = $percent;
        return $this;
    }

    public function clientOrderId(string $clientOrderId): self
    {
        $this->orderData['clientOrderId'] = $clientOrderId;
        return $this;
    }

    public function timeInForce(string $timeInForce): self
    {
        $this->orderData['timeInForce'] = $timeInForce;
        return $this;
    }

    public function reduceOnly(bool $reduceOnly = true): self
    {
        $this->orderData['reduceOnly'] = $reduceOnly;
        return $this;
    }

    public function closePosition(bool $closePosition = true): self
    {
        $this->orderData['closePosition'] = $closePosition;
        return $this;
    }

    public function stopPrice(float $price): self
    {
        if ($price <= 0) {
            $this->addError("Stop price must be greater than 0");
            return $this;
        }

        $this->orderData['stopPrice'] = $price;
        return $this;
    }

    public function stopGuaranteed(bool $flag = true): self
    {
        $this->orderData['stopGuaranteed'] = $flag;
        return $this;
    }

    public function priceRate(float $rate): self
    {
        if ($rate <= 0) {
            $this->addError("Price rate must be greater than 0");
            return $this;
        }

        $this->orderData['priceRate'] = $rate;
        return $this;
    }

    public function workingType(string $workingType): self
    {
        $this->orderData['workingType'] = $workingType;
        return $this;
    }

    public function newOrderRespType(string $type): self
    {
        $this->orderData['newOrderRespType'] = $type;
        return $this;
    }

    public function positionId(int $positionId): self
    {
        if ($positionId <= 0) {
            $this->addError("Position ID must be greater than 0");
            return $this;
        }

        $this->orderData['positionId'] = $positionId;
        return $this;
    }

    public function timestamp(int $timestamp): self
    {
        $this->orderData['timestamp'] = $timestamp;
        return $this;
    }

    public function recvWindow(int $recvWindow): self
    {
        if ($recvWindow <= 0) {
            $this->addError("recvWindow must be greater than 0");
            return $this;
        }

        $this->orderData['recvWindow'] = $recvWindow;
        return $this;
    }

    /**
     * Validate order data
     */
    protected function validate(): bool
    {
        if (!empty($this->errors)) {
            return false;
        }

        // Check required fields
        $required = ['symbol', 'side', 'type'];
        foreach ($required as $field) {
            if (!isset($this->orderData[$field])) {
                $this->addError("Missing required field: {$field}");
            }
        }

        // Spot order validation
        if ($this->orderType === 'spot') {
            if (!isset($this->orderData['quantity'])) {
                $this->addError("Spot orders require quantity");
            }
            if (isset($this->orderData['positionSide'])) {
                $this->addError("Position side not available for spot orders");
            }
        }

        // Futures order validation
        if ($this->orderType === 'futures') {
            if (!isset($this->orderData['positionSide'])) {
                $this->addError("Futures orders require position side (LONG/SHORT)");
            }
            if (!isset($this->orderData['margin']) && !isset($this->orderData['quantity'])) {
                $this->addError("Futures orders require margin or quantity");
            }
        }

        // reduceOnly / closePosition / positionId are only valid for futures orders
        if ($this->orderType !== 'futures') {
            if (isset($this->orderData['reduceOnly'])) {
                $this->addError("reduceOnly is only available for futures orders");
            }
            if (isset($this->orderData['closePosition'])) {
                $this->addError("closePosition is only available for futures orders");
            }
            if (isset($this->orderData['positionId'])) {
                $this->addError("positionId is only available for futures orders");
            }
        }

        // Conflicting settings: reduceOnly + closePosition at the same time
        if (
            isset($this->orderData['reduceOnly'], $this->orderData['closePosition']) &&
            $this->orderData['reduceOnly'] === true &&
            $this->orderData['closePosition'] === true
        ) {
            $this->addError("reduceOnly and closePosition cannot both be true");
        }

        // Limit order validation
        if (isset($this->orderData['type']) && $this->orderData['type'] === 'LIMIT') {
            if (!isset($this->orderData['price'])) {
                $this->addError("LIMIT orders require price");
            }
        }

        return empty($this->errors);
    }

    /**
     * Add validation error
     */
    protected function addError(string $error): void
    {
        $this->errors[] = $error;
        $this->isValid = false;
    }

    /**
     * Get validation errors
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Check if order is valid
     */
    public function isValid(): bool
    {
        return $this->validate();
    }

    /**
     * Build final order data
     */
    protected function buildOrderData(): array
    {
        $data = $this->orderData;

        // Calculate stop loss/take profit from percentages if needed
        if (isset($data['stopLossPercent']) && isset($data['price'])) {
            $side = $data['side'];
            $price = $data['price'];
            $percent = $data['stopLossPercent'];
            
            if ($side === 'BUY') {
                $data['stopLoss'] = $price * (1 - $percent / 100);
            } else {
                $data['stopLoss'] = $price * (1 + $percent / 100);
            }
            unset($data['stopLossPercent']);
        }

        if (isset($data['takeProfitPercent']) && isset($data['price'])) {
            $side = $data['side'];
            $price = $data['price'];
            $percent = $data['takeProfitPercent'];
            
            if ($side === 'BUY') {
                $data['takeProfit'] = $price * (1 + $percent / 100);
            } else {
                $data['takeProfit'] = $price * (1 - $percent / 100);
            }
            unset($data['takeProfitPercent']);
        }

        return $data;
    }

    /**
     * Execute the order
     */
    public function execute(): array
    {
        if (!$this->validate()) {
            throw new \InvalidArgumentException('Invalid order: ' . implode(', ', $this->errors));
        }

        $orderData = $this->buildOrderData();

        // Set leverage first for futures orders
        if ($this->orderType === 'futures' && isset($orderData['leverage'])) {
            // Use positionSide when available, otherwise BOTH (one-way mode)
            $side = $orderData['positionSide'] ?? 'BOTH';
            $this->trade->changeLeverage($orderData['symbol'], $side, $orderData['leverage']);
        }

        // Use test endpoint if this is a test order
        if ($this->isTestOrder) {
            return $this->trade->createTestOrder($orderData);
        }

        return $this->trade->createOrder($orderData);
    }

    /**
     * Get order data without executing
     */
    public function getOrderData(): array
    {
        if (!$this->validate()) {
            throw new \InvalidArgumentException('Invalid order: ' . implode(', ', $this->errors));
        }

        return $this->buildOrderData();
    }
}
