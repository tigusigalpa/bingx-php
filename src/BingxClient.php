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
use Tigusigalpa\BingX\Services\TwapService;
use Tigusigalpa\BingX\Services\SpotTradeService;
use Tigusigalpa\BingX\WebSocket\AccountDataStream;
use Tigusigalpa\BingX\WebSocket\MarketDataStream;

class BingxClient
{
    public const ENVIRONMENT_LIVE = 'live';
    public const ENVIRONMENT_DEMO = 'demo';
    public const LIVE_BASE_URI = BaseHttpClient::LIVE_BASE_URI;
    public const DEMO_BASE_URI = BaseHttpClient::DEMO_BASE_URI;

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
    protected TwapService $twap;
    protected SpotTradeService $spotTrade;
    protected ?CoinMClient $coinMClient = null;
    protected ?TradFiClient $tradFiClient = null;

    public function __construct(
        string $apiKey, 
        string $apiSecret, 
        string $baseUri = self::LIVE_BASE_URI,
        ?string $sourceKey = null, 
        string $signatureEncoding = 'hex',
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
        $this->twap = new TwapService($this->httpClient);
        $this->spotTrade = new SpotTradeService($this->httpClient);
    }

    /**
     * Create a client for BingX Virtual Simulation Trading (VST).
     *
     * VST uses virtual funds while retaining the same service surface as the
     * production client. Use a VST API key issued by BingX.
     */
    public static function newDemoClient(
        string $apiKey,
        string $apiSecret,
        ?string $sourceKey = null,
        string $signatureEncoding = 'hex'
    ): self {
        return new self(
            $apiKey,
            $apiSecret,
            self::DEMO_BASE_URI,
            $sourceKey,
            $signatureEncoding
        );
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
     * Get Spot Trade Service for placing and managing spot orders.
     */
    public function spotTrade(): SpotTradeService
    {
        return $this->spotTrade;
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
     * Get TWAP Service for algorithmic trading (API v3)
     * 
     * TWAP (Time-Weighted Average Price) orders execute large trades over time
     * to minimize market impact.
     * 
     * @return TwapService
     */
    public function twap(): TwapService
    {
        return $this->twap;
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
     * Get the Traditional Finance client for stock, forex, commodity, and
     * index perpetual instruments.
     */
    public function tradFi(): TradFiClient
    {
        if ($this->tradFiClient === null) {
            $this->tradFiClient = new TradFiClient($this->httpClient);
        }

        return $this->tradFiClient;
    }

    /**
     * Create a public market-data WebSocket stream.
     *
     * The default URL is BingX's public swap stream. Supply an explicit URL
     * when BingX provides an environment-specific stream for your account.
     */
    public function marketDataStream(?string $url = null): MarketDataStream
    {
        return new MarketDataStream($url);
    }

    /**
     * Create a private account-data WebSocket stream for a listen key.
     *
     * The caller may provide an environment-specific URL when one is supplied
     * by BingX. This avoids silently routing a VST account to an invented URL.
     */
    public function accountDataStream(string $listenKey, ?string $baseUrl = null): AccountDataStream
    {
        return new AccountDataStream($listenKey, $baseUrl);
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
     * Whether this client uses BingX Virtual Simulation Trading (VST).
     */
    public function isDemo(): bool
    {
        return $this->httpClient->isDemo();
    }

    /**
     * Return the active exchange environment: "live" or "demo".
     */
    public function getEnvironment(): string
    {
        return $this->isDemo() ? self::ENVIRONMENT_DEMO : self::ENVIRONMENT_LIVE;
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
