<?php

namespace Tigusigalpa\BingX;

use Tigusigalpa\BingX\Http\BaseHttpClient;
use Tigusigalpa\BingX\Services\AccountService;
use Tigusigalpa\BingX\Services\ListenKeyService;
use Tigusigalpa\BingX\Services\MarketService;
use Tigusigalpa\BingX\Services\TradeService;

/**
 * Entry point for BingX Traditional Finance perpetual instruments.
 *
 * Stocks, forex, commodities, and indices are traded through the same
 * authenticated swap API as USDT-M perpetuals, but this separate accessor
 * keeps that intent explicit in Laravel applications.
 */
class TradFiClient
{
    protected MarketService $market;
    protected TradeService $trade;
    protected AccountService $account;
    protected ListenKeyService $listenKey;

    public function __construct(BaseHttpClient $httpClient)
    {
        $this->market = new MarketService($httpClient);
        $this->trade = new TradeService($httpClient);
        $this->account = new AccountService($httpClient);
        $this->listenKey = new ListenKeyService($httpClient);
    }

    public function market(): MarketService
    {
        return $this->market;
    }

    public function trade(): TradeService
    {
        return $this->trade;
    }

    public function account(): AccountService
    {
        return $this->account;
    }

    public function listenKey(): ListenKeyService
    {
        return $this->listenKey;
    }
}
