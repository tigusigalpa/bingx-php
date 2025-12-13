<?php

namespace Tigusigalpa\BingX;

use Tigusigalpa\BingX\Http\BaseHttpClient;
use Tigusigalpa\BingX\Services\MarketService;
use Tigusigalpa\BingX\Services\AccountService;
use Tigusigalpa\BingX\Services\TradeService;
use Tigusigalpa\BingX\Services\ContractService;
use Tigusigalpa\BingX\Services\ListenKeyService;
use Tigusigalpa\BingX\Services\WalletService;
use Tigusigalpa\BingX\Services\SpotAccountService;
use Tigusigalpa\BingX\Services\SubAccountService;
use Tigusigalpa\BingX\Services\CopyTradingService;

class BingxClient
{
    protected BaseHttpClient $httpClient;
    protected MarketService $market;
    protected AccountService $account;
    protected TradeService $trade;
    protected ContractService $contract;
    protected ListenKeyService $listenKey;
    protected WalletService $wallet;
    protected SpotAccountService $spotAccount;
    protected SubAccountService $subAccount;
    protected CopyTradingService $copyTrading;
    protected ?CoinMClient $coinMClient = null;

    public function __construct(
        string $apiKey, 
        string $apiSecret, 
        string $baseUri = 'https://open-api.bingx.com', 
        ?string $sourceKey = null, 
        string $signatureEncoding = 'base64', 
        ?BaseHttpClient $httpClient = null
    ) {
        $this->httpClient = $httpClient ?: new BaseHttpClient(
            $apiKey,
            $apiSecret,
            $baseUri,
            $sourceKey,
            $signatureEncoding
        );

        $this->market = new MarketService($this->httpClient);
        $this->account = new AccountService($this->httpClient);
        $this->trade = new TradeService($this->httpClient);
        $this->contract = new ContractService($this->httpClient);
        $this->listenKey = new ListenKeyService($this->httpClient);
        $this->wallet = new WalletService($this->httpClient);
        $this->spotAccount = new SpotAccountService($this->httpClient);
        $this->subAccount = new SubAccountService($this->httpClient);
        $this->copyTrading = new CopyTradingService($this->httpClient);
    }

    /**
     * Get Market Service for market data operations
     * 
     * @return MarketService
     */
    public function market(): MarketService
    {
        return $this->market;
    }

    /**
     * Get Account Service for account operations
     * 
     * @return AccountService
     */
    public function account(): AccountService
    {
        return $this->account;
    }

    /**
     * Get Trade Service for trading operations
     * 
     * @return TradeService
     */
    public function trade(): TradeService
    {
        return $this->trade;
    }

    /**
     * Get Contract Service for standard contract operations
     * 
     * @return ContractService
     */
    public function contract(): ContractService
    {
        return $this->contract;
    }

    /**
     * Get Listen Key Service for WebSocket authentication
     * 
     * @return ListenKeyService
     */
    public function listenKey(): ListenKeyService
    {
        return $this->listenKey;
    }

    /**
     * Get Wallet Service for wallet operations (deposits, withdrawals)
     * 
     * @return WalletService
     */
    public function wallet(): WalletService
    {
        return $this->wallet;
    }

    /**
     * Get Spot Account Service for spot account operations
     * 
     * @return SpotAccountService
     */
    public function spotAccount(): SpotAccountService
    {
        return $this->spotAccount;
    }

    /**
     * Get Sub-Account Service for sub-account management operations
     * 
     * @return SubAccountService
     */
    public function subAccount(): SubAccountService
    {
        return $this->subAccount;
    }

    /**
     * Get Copy Trading Service for copy trading operations
     * 
     * @return CopyTradingService
     */
    public function copyTrading(): CopyTradingService
    {
        return $this->copyTrading;
    }

    /**
     * Get Coin-M Perpetual Futures Client
     * 
     * Provides access to Coin-Margined perpetual futures API.
     * These contracts are margined and settled in cryptocurrency (BTC, ETH, etc.)
     * instead of USDT.
     * 
     * @return CoinMClient
     * 
     * @example
     * // Get Coin-M market data
     * $ticker = Bingx::coinM()->market()->getTicker('BTC-USD');
     * 
     * // Place Coin-M order
     * $order = Bingx::coinM()->trade()->createOrder([
     *     'symbol' => 'BTC-USD',
     *     'side' => 'BUY',
     *     'positionSide' => 'LONG',
     *     'type' => 'MARKET',
     *     'quantity' => 100
     * ]);
     */
    public function coinM(): CoinMClient
    {
        if ($this->coinMClient === null) {
            $this->coinMClient = new CoinMClient($this->httpClient);
        }
        
        return $this->coinMClient;
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

    /**
     * Get API endpoint URL
     * 
     * @return string
     */
    public function getEndpoint(): string
    {
        return $this->httpClient->getEndpoint();
    }

    /**
     * Get API key
     * 
     * @return string
     */
    public function getApiKey(): string
    {
        return $this->httpClient->getApiKey();
    }

    // Legacy methods for backward compatibility
    public function getBalance(): array
    {
        return $this->account->getBalance();
    }

    public function getSymbols(): array
    {
        return $this->market->getSymbols();
    }

    public function createOrder(array $params): array
    {
        return $this->trade->createOrder($params);
    }
}