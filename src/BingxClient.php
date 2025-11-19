<?php

namespace Tigusigalpa\BingX;

use Tigusigalpa\BingX\Http\BaseHttpClient;
use Tigusigalpa\BingX\Services\MarketService;
use Tigusigalpa\BingX\Services\AccountService;
use Tigusigalpa\BingX\Services\TradeService;
use Tigusigalpa\BingX\Services\ContractService;
use Tigusigalpa\BingX\Services\ListenKeyService;

class BingxClient
{
    protected BaseHttpClient $httpClient;
    protected MarketService $market;
    protected AccountService $account;
    protected TradeService $trade;
    protected ContractService $contract;
    protected ListenKeyService $listenKey;

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