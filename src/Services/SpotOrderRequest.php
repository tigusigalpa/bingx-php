<?php

namespace Tigusigalpa\BingX\Services;

/**
 * Validated convenience request for BingX spot LIMIT and MARKET orders.
 *
 * Decimal values are strings deliberately. A PHP float can change the value
 * sent to the exchange (for example, 0.1 is not represented exactly in
 * binary), which can result in an invalid tick or order size.
 */
class SpotOrderRequest
{
    public string $symbol;
    public string $side;
    public string $type;
    public string $quantity;
    public ?string $price;
    public ?string $quoteOrderQty;
    public ?string $timeInForce;
    public ?string $clientOrderId;

    public function __construct(
        string $symbol,
        string $side,
        string $type,
        string $quantity = '',
        ?string $price = null,
        ?string $quoteOrderQty = null,
        ?string $timeInForce = null,
        ?string $clientOrderId = null
    ) {
        $this->symbol = $symbol;
        $this->side = $side;
        $this->type = $type;
        $this->quantity = $quantity;
        $this->price = $price;
        $this->quoteOrderQty = $quoteOrderQty;
        $this->timeInForce = $timeInForce;
        $this->clientOrderId = $clientOrderId;
    }
}
