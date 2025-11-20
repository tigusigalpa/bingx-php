<?php

namespace Tigusigalpa\BingX\Services;

use Tigusigalpa\BingX\Http\BaseHttpClient;

class SpotAccountService
{
    protected BaseHttpClient $client;

    public function __construct(BaseHttpClient $client)
    {
        $this->client = $client;
    }

    /**
     * Get spot account balance
     * 
     * @param int|null $recvWindow Request valid time window in milliseconds
     * @return array
     */
    public function getBalance(?int $recvWindow = null): array
    {
        $params = [];
        
        if ($recvWindow !== null) {
            $params['recvWindow'] = $recvWindow;
        }

        return $this->client->request('GET', '/openApi/spot/v1/account/balance', $params);
    }

    /**
     * Query asset transfer records
     * 
     * @param string $type Transfer type (FUND_SFUTURES, SFUTURES_FUND, FUND_PFUTURES, PFUTURES_FUND, etc.)
     * @param int|null $startTime Start timestamp in milliseconds
     * @param int|null $endTime End timestamp in milliseconds
     * @param int|null $current Current page number (default 1)
     * @param int|null $size Page size (default 10, max 100)
     * @return array
     */
    public function getAssetTransferRecords(
        string $type,
        ?int $startTime = null,
        ?int $endTime = null,
        ?int $current = null,
        ?int $size = null
    ): array {
        $params = ['type' => $type];
        
        if ($startTime !== null) $params['startTime'] = $startTime;
        if ($endTime !== null) $params['endTime'] = $endTime;
        if ($current !== null) $params['current'] = $current;
        if ($size !== null) $params['size'] = $size;

        return $this->client->request('GET', '/openApi/api/v3/asset/transfer', $params);
    }

    /**
     * User universal transfer (Internal transfer between accounts)
     * 
     * @param string $type Transfer type (FUND_SFUTURES, SFUTURES_FUND, FUND_PFUTURES, PFUTURES_FUND, etc.)
     * @param string $asset Asset name
     * @param float $amount Transfer amount
     * @return array
     */
    public function universalTransfer(string $type, string $asset, float $amount): array
    {
        return $this->client->request('POST', '/openApi/api/asset/v1/transfer', [
            'type' => $type,
            'asset' => $asset,
            'amount' => $amount
        ]);
    }

    /**
     * Internal transfer (between main and sub-accounts)
     * 
     * @param string $coin Coin name
     * @param string $walletType Wallet type (SPOT, PERPETUAL)
     * @param float $amount Transfer amount
     * @param string $transferType Transfer type (FROM_MAIN_TO_SUB, FROM_SUB_TO_MAIN)
     * @param string|null $subUid Sub-account UID
     * @param string|null $clientId Client order ID
     * @return array
     */
    public function internalTransfer(
        string $coin,
        string $walletType,
        float $amount,
        string $transferType,
        ?string $subUid = null,
        ?string $clientId = null
    ): array {
        $params = [
            'coin' => $coin,
            'walletType' => $walletType,
            'amount' => $amount,
            'transferType' => $transferType
        ];
        
        if ($subUid !== null) $params['subUid'] = $subUid;
        if ($clientId !== null) $params['clientId'] = $clientId;

        return $this->client->request('POST', '/openApi/wallets/v1/capital/innerTransfer/apply', $params);
    }

    /**
     * Query transferable currencies
     * 
     * @return array
     */
    public function getSupportedTransferCoins(): array
    {
        return $this->client->request('GET', '/openApi/api/asset/v1/transfer/supportCoins');
    }

    /**
     * Query internal transfer records
     * 
     * @param string|null $clientId Client order ID
     * @param int|null $startTime Start timestamp in milliseconds
     * @param int|null $endTime End timestamp in milliseconds
     * @param int|null $current Current page number (default 1)
     * @param int|null $size Page size (default 10, max 100)
     * @return array
     */
    public function getInternalTransferRecords(
        ?string $clientId = null,
        ?int $startTime = null,
        ?int $endTime = null,
        ?int $current = null,
        ?int $size = null
    ): array {
        $params = [];
        
        if ($clientId !== null) $params['clientId'] = $clientId;
        if ($startTime !== null) $params['startTime'] = $startTime;
        if ($endTime !== null) $params['endTime'] = $endTime;
        if ($current !== null) $params['current'] = $current;
        if ($size !== null) $params['size'] = $size;

        return $this->client->request('GET', '/openApi/api/v3/asset/transferRecord', $params);
    }

    /**
     * Get fund account balance
     * 
     * @return array
     */
    public function getFundBalance(): array
    {
        return $this->client->request('GET', '/openApi/fund/v1/account/balance');
    }

    /**
     * Get main account internal transfer records
     * 
     * @param string|null $clientId Client order ID
     * @param int|null $startTime Start timestamp in milliseconds
     * @param int|null $endTime End timestamp in milliseconds
     * @param int|null $current Current page number (default 1)
     * @param int|null $size Page size (default 10, max 100)
     * @return array
     */
    public function getMainAccountInternalTransferRecords(
        ?string $clientId = null,
        ?int $startTime = null,
        ?int $endTime = null,
        ?int $current = null,
        ?int $size = null
    ): array {
        $params = [];
        
        if ($clientId !== null) $params['clientId'] = $clientId;
        if ($startTime !== null) $params['startTime'] = $startTime;
        if ($endTime !== null) $params['endTime'] = $endTime;
        if ($current !== null) $params['current'] = $current;
        if ($size !== null) $params['size'] = $size;

        return $this->client->request('GET', '/openApi/wallets/v1/capital/innerTransfer/records', $params);
    }

    /**
     * Get all account balances
     * 
     * @return array
     */
    public function getAllAccountBalances(): array
    {
        return $this->client->request('GET', '/openApi/account/v1/allAccountBalance');
    }
}
