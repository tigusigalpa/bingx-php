<?php

namespace Tigusigalpa\BingX;

use Tigusigalpa\BingX\Http\BaseHttpClient;
use Tigusigalpa\BingX\Services\CoinM\MarketService;
use Tigusigalpa\BingX\Services\CoinM\TradeService;
use Tigusigalpa\BingX\Services\CoinM\ListenKeyService;

/**
 * Coin-M Perpetual Futures Client
 * 
 * Provides access to Coin-Margined perpetual futures API.
 * These contracts are margined and settled in cryptocurrency (BTC, ETH, etc.)
 * instead of USDT.
 * 
 * Key differences from USDT-M:
 * - Margin currency: Cryptocurrency (BTC, ETH) instead of USDT
 * - Settlement: In base cryptocurrency
 * - API path: /openApi/cswap/v1/ instead of /openApi/swap/v2/
 * - Symbol format: BTC-USD, ETH-USD instead of BTC-USDT
 * 
 * @example
 * // Via main client
 * $bingx = new BingxClient($apiKey, $apiSecret);
 * $coinM = $bingx->coinM();
 * 
 * // Get BTC-USD price
 * $ticker = $coinM->market()->getTicker('BTC-USD');
 * 
 * // Place order
 * $order = $coinM->trade()->createOrder([
 *     'symbol' => 'BTC-USD',
 *     'side' => 'BUY',
 *     'positionSide' => 'LONG',
 *     'type' => 'MARKET',
 *     'quantity' => 100
 * ]);
 */
class CoinMClient
{
    protected BaseHttpClient $httpClient;
    protected MarketService $market;
    protected TradeService $trade;
    protected ListenKeyService $listenKey;

    /**
     * Create a new Coin-M client instance
     * 
     * @param BaseHttpClient $httpClient HTTP client with authentication
     */
    public function __construct(BaseHttpClient $httpClient)
    {
        $this->httpClient = $httpClient;
        $this->market = new MarketService($httpClient);
        $this->trade = new TradeService($httpClient);
        $this->listenKey = new ListenKeyService($httpClient);
    }

    /**
     * Get Market Service for Coin-M market data
     * 
     * Provides access to:
     * - Contract information
     * - Price and funding rate
     * - Open interest
     * - K-line data
     * - Order book depth
     * - 24-hour statistics
     * 
     * @return MarketService
     * 
     * @example
     * $contracts = $coinM->market()->getContracts();
     * $ticker = $coinM->market()->getTicker('BTC-USD');
     * $depth = $coinM->market()->getDepth('BTC-USD', 20);
     */
    public function market(): MarketService
    {
        return $this->market;
    }

    /**
     * Get Trade Service for Coin-M trading operations
     * 
     * Provides access to:
     * - Order placement and management
     * - Position queries and management
     * - Leverage and margin settings
     * - Account balance
     * - Order history
     * 
     * @return TradeService
     * 
     * @example
     * $order = $coinM->trade()->createOrder([...]);
     * $positions = $coinM->trade()->getPositions('BTC-USD');
     * $balance = $coinM->trade()->getBalance();
     */
    public function trade(): TradeService
    {
        return $this->trade;
    }

    /**
     * Get Listen Key Service for WebSocket authentication
     * 
     * Manages listen keys for private WebSocket streams:
     * - Account balance updates
     * - Position updates
     * - Order updates
     * 
     * @return ListenKeyService
     * 
     * @example
     * $response = $coinM->listenKey()->generate();
     * $listenKey = $response['listenKey'];
     * 
     * // Use for WebSocket connection
     * // Extend every 30 minutes
     * $coinM->listenKey()->extend($listenKey);
     * 
     * // Delete when done
     * $coinM->listenKey()->delete($listenKey);
     */
    public function listenKey(): ListenKeyService
    {
        return $this->listenKey;
    }

    /**
     * Get the underlying HTTP client
     * 
     * @return BaseHttpClient
     */
    public function getHttpClient(): BaseHttpClient
    {
        return $this->httpClient;
    }
}
