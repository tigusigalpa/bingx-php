<?php

namespace Tigusigalpa\BingX\Services;

use Tigusigalpa\BingX\Http\BaseHttpClient;

class AccountService
{
    protected BaseHttpClient $client;

    public function __construct(BaseHttpClient $client)
    {
        $this->client = $client;
    }

    /**
     * Get account balance
     * 
     * @return array
     */
    public function getBalance(): array
    {
        return $this->client->request('GET', '/openApi/swap/v2/user/balance');
    }

    /**
     * Get positions
     * 
     * @param string|null $symbol Trading symbol (optional)
     * @return array
     */
    public function getPositions(?string $symbol = null): array
    {
        $params = [];
        if ($symbol) $params['symbol'] = $symbol;

        return $this->client->request('GET', '/openApi/swap/v2/user/positions', $params);
    }

    /**
     * Get account information
     * 
     * @return array
     */
    public function getAccountInfo(): array
    {
        return $this->client->request('GET', '/openApi/swap/v2/user/account');
    }

    /**
     * Get trading fees
     * 
     * @param string $symbol Trading symbol
     * @return array
     */
    public function getTradingFees(string $symbol): array
    {
        return $this->client->request('GET', '/openApi/swap/v2/user/tradingFees', [
            'symbol' => $symbol
        ]);
    }

    /**
     * Get margin mode
     * 
     * @param string $symbol Trading symbol
     * @return array
     */
    public function getMarginMode(string $symbol): array
    {
        return $this->client->request('GET', '/openApi/swap/v2/user/getMarginMode', [
            'symbol' => $symbol
        ]);
    }

    /**
     * Set margin mode
     * 
     * @param string $symbol Trading symbol
     * @param string $marginMode Margin mode (ISOLATED, CROSSED)
     * @return array
     */
    public function setMarginMode(string $symbol, string $marginMode): array
    {
        return $this->client->request('POST', '/openApi/swap/v2/user/setMarginMode', [
            'symbol' => $symbol,
            'marginMode' => $marginMode
        ]);
    }

    /**
     * Get leverage (swap V2 trade Get Leverage endpoint)
     *
     * According to BingX swap V2 trade API docs, parameters are:
     * - symbol: trading pair symbol with hyphen (e.g. BTC-USDT)
     * - timestamp: request timestamp in milliseconds
     * - recvWindow (optional): request valid time window in ms
     *
     * @param string $symbol Trading symbol
     * @param int|null $recvWindow Optional recvWindow in milliseconds
     * @return array
     */
    public function getLeverage(string $symbol, ?int $recvWindow = null): array
    {
        $params = [
            'symbol'    => $symbol,
            'timestamp' => (int) (microtime(true) * 1000),
        ];

        if ($recvWindow !== null) {
            $params['recvWindow'] = $recvWindow;
        }

        return $this->client->request('GET', '/openApi/swap/v2/trade/leverage', $params);
    }

    /**
     * Set leverage (delegates to swap V2 trade Set Leverage endpoint)
     *
     * Required parameters (per BingX docs):
     * - symbol: trading pair symbol with hyphen (e.g. BTC-USDT)
     * - side: leverage side for positions (LONG, SHORT, BOTH)
     * - leverage: leverage value
     * - timestamp: request timestamp in milliseconds
     * - recvWindow (optional): request valid time window in ms
     *
     * @param string $symbol Trading symbol
     * @param string $side Leverage side (LONG, SHORT, BOTH)
     * @param int $leverage Leverage value
     * @param int|null $recvWindow Optional recvWindow in milliseconds
     * @return array
     */
    public function setLeverage(string $symbol, string $side, int $leverage, ?int $recvWindow = null): array
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
     * Get position margin
     * 
     * @param string $symbol Trading symbol
     * @return array
     */
    public function getPositionMargin(string $symbol): array
    {
        return $this->client->request('GET', '/openApi/swap/v2/user/getPositionMargin', [
            'symbol' => $symbol
        ]);
    }

    /**
     * Set position margin
     * 
     * @param string $symbol Trading symbol
     * @param string $positionSide Position side (LONG, SHORT, BOTH)
     * @param float $amount Margin amount
     * @param int $type Margin type (1: ADD, 2: REDUCE)
     * @return array
     */
    public function setPositionMargin(string $symbol, string $positionSide, float $amount, int $type): array
    {
        return $this->client->request('POST', '/openApi/swap/v2/user/setPositionMargin', [
            'symbol' => $symbol,
            'positionSide' => $positionSide,
            'amount' => $amount,
            'type' => $type
        ]);
    }

    /**
     * Get history of balance changes
     * 
     * @param string $coin Coin name
     * @param int $limit Number of records (1-100)
     * @return array
     */
    public function getBalanceHistory(string $coin, int $limit = 100): array
    {
        return $this->client->request('GET', '/openApi/swap/v2/user/balanceHistory', [
            'coin' => $coin,
            'limit' => $limit
        ]);
    }

    /**
     * Get account API trading permissions
     * 
     * @return array Account permissions information
     */
    public function getAccountPermissions(): array
    {
        return $this->client->request('GET', '/openApi/swap/v2/user/apiPermissions');
    }

    /**
     * Get user API key information
     * 
     * @return array API key information
     */
    public function getApiKey(): array
    {
        return $this->client->request('GET', '/openApi/swap/v2/user/apiKey');
    }

    /**
     * Get deposit history
     * 
     * @param string $coin Coin name (optional)
     * @param int $limit Number of records (1-1000)
     * @param int|null $startTime Start timestamp in milliseconds
     * @param int|null $endTime End timestamp in milliseconds
     * @return array
     */
    public function getDepositHistory(?string $coin = null, int $limit = 100, ?int $startTime = null, ?int $endTime = null): array
    {
        $params = ['limit' => $limit];
        
        if ($coin) $params['coin'] = $coin;
        if ($startTime) $params['startTime'] = $startTime;
        if ($endTime) $params['endTime'] = $endTime;

        return $this->client->request('GET', '/openApi/swap/v2/user/depositHistory', $params);
    }

    /**
     * Get withdrawal history
     * 
     * @param string $coin Coin name (optional)
     * @param int $limit Number of records (1-1000)
     * @param int|null $startTime Start timestamp in milliseconds
     * @param int|null $endTime End timestamp in milliseconds
     * @return array
     */
    public function getWithdrawHistory(?string $coin = null, int $limit = 100, ?int $startTime = null, ?int $endTime = null): array
    {
        $params = ['limit' => $limit];
        
        if ($coin) $params['coin'] = $coin;
        if ($startTime) $params['startTime'] = $startTime;
        if ($endTime) $params['endTime'] = $endTime;

        return $this->client->request('GET', '/openApi/swap/v2/user/withdrawHistory', $params);
    }

    /**
     * Get asset details
     * 
     * @param string $asset Asset name
     * @return array
     */
    public function getAssetDetails(string $asset): array
    {
        return $this->client->request('GET', '/openApi/swap/v2/user/asset', [
            'asset' => $asset
        ]);
    }

    /**
     * Get all assets
     * 
     * @return array List of all available assets
     */
    public function getAllAssets(): array
    {
        return $this->client->request('GET', '/openApi/swap/v2/user/assets');
    }

    /**
     * Get universal transfer history
     * 
     * @param string $type Transfer type (UMFUTURE_MAIN, MAIN_UMFUTURE, etc.)
     * @param int $limit Number of records (1-100)
     * @param int|null $startTime Start timestamp in milliseconds
     * @param int|null $endTime End timestamp in milliseconds
     * @return array
     */
    public function getUniversalTransferHistory(string $type, int $limit = 100, ?int $startTime = null, ?int $endTime = null): array
    {
        $params = [
            'type' => $type,
            'limit' => $limit
        ];
        
        if ($startTime) $params['startTime'] = $startTime;
        if ($endTime) $params['endTime'] = $endTime;

        return $this->client->request('GET', '/openApi/swap/v2/user/universalTransferHistory', $params);
    }

    /**
     * Get funding wallet
     * 
     * @param string $asset Asset name (optional)
     * @return array
     */
    public function getFundingWallet(?string $asset = null): array
    {
        $params = [];
        if ($asset) $params['asset'] = $asset;

        return $this->client->request('GET', '/openApi/swap/v2/user/fundingWallet', $params);
    }

    /**
     * Get account API rate limits
     * 
     * @return array API rate limit information
     */
    public function getApiRateLimits(): array
    {
        return $this->client->request('GET', '/openApi/swap/v2/user/rateLimit');
    }

    /**
     * Create universal transfer
     * 
     * @param string $type Transfer type (UMFUTURE_MAIN, MAIN_UMFUTURE, etc.)
     * @param string $asset Asset name
     * @param float $amount Transfer amount
     * @return array
     */
    public function createUniversalTransfer(string $type, string $asset, float $amount): array
    {
        return $this->client->request('POST', '/openApi/swap/v2/user/universalTransfer', [
            'type' => $type,
            'asset' => $asset,
            'amount' => $amount
        ]);
    }

    /**
     * Get dust log (small asset conversion history)
     * 
     * @param int $limit Number of records (1-1000)
     * @param int|null $startTime Start timestamp in milliseconds
     * @param int|null $endTime End timestamp in milliseconds
     * @return array
     */
    public function getDustLog(int $limit = 100, ?int $startTime = null, ?int $endTime = null): array
    {
        $params = ['limit' => $limit];
        
        if ($startTime) $params['startTime'] = $startTime;
        if ($endTime) $params['endTime'] = $endTime;

        return $this->client->request('GET', '/openApi/swap/v2/user/dustLog', $params);
    }

    /**
     * Convert dust assets to BNB
     * 
     * @param array $assets Array of asset names to convert
     * @return array
     */
    public function dustTransfer(array $assets): array
    {
        return $this->client->request('POST', '/openApi/swap/v2/user/dustTransfer', [
            'assets' => json_encode($assets)
        ]);
    }

    /**
     * Get asset dividend records
     * 
     * @param string $asset Asset name (optional)
     * @param int $limit Number of records (1-1000)
     * @param int|null $startTime Start timestamp in milliseconds
     * @param int|null $endTime End timestamp in milliseconds
     * @return array
     */
    public function getAssetDividendRecord(?string $asset = null, int $limit = 100, ?int $startTime = null, ?int $endTime = null): array
    {
        $params = ['limit' => $limit];
        
        if ($asset) $params['asset'] = $asset;
        if ($startTime) $params['startTime'] = $startTime;
        if ($endTime) $params['endTime'] = $endTime;

        return $this->client->request('GET', '/openApi/swap/v2/user/assetDividend', $params);
    }

    /**
     * Get user commission rates
     * 
     * @param string|null $symbol Trading symbol (optional)
     * @return array
     */
    public function getUserCommissionRates(?string $symbol = null): array
    {
        $params = [];
        if ($symbol) $params['symbol'] = $symbol;

        return $this->client->request('GET', '/openApi/swap/v2/user/commissionRates', $params);
    }

    /**
     * Get API key permissions
     * 
     * @return array API key permissions
     */
    public function getApiKeyPermissions(): array
    {
        return $this->client->request('GET', '/openApi/swap/v2/user/apiKeyPermissions');
    }

    /**
     * Enable fast withdraw switch
     * 
     * @return array
     */
    public function enableFastWithdrawSwitch(): array
    {
        return $this->client->request('POST', '/openApi/swap/v2/user/enableFastWithdrawSwitch');
    }

    /**
     * Disable fast withdraw switch
     * 
     * @return array
     */
    public function disableFastWithdrawSwitch(): array
    {
        return $this->client->request('POST', '/openApi/swap/v2/user/disableFastWithdrawSwitch');
    }

    /**
     * Get account API permissions
     * 
     * @return array Account API permissions information
     */
    public function getAccountApiPermissions(): array
    {
        return $this->client->request('GET', '/openApi/v1/account/apiPermissions');
    }
}
