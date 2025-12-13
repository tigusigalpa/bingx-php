<?php

namespace Tigusigalpa\BingX\Services;

use Tigusigalpa\BingX\Http\BaseHttpClient;

class SubAccountService
{
    protected BaseHttpClient $client;

    public function __construct(BaseHttpClient $client)
    {
        $this->client = $client;
    }

    /**
     * Create sub-account
     * 
     * @param string $subAccountString Sub-account identifier
     * @return array
     */
    public function createSubAccount(string $subAccountString): array
    {
        return $this->client->request('POST', '/openApi/subAccount/v1/create', [
            'subAccountString' => $subAccountString
        ]);
    }

    /**
     * Get account UID
     * 
     * @return array
     */
    public function getAccountUid(): array
    {
        return $this->client->request('GET', '/openApi/account/v1/uid');
    }

    /**
     * Get sub-account list
     * 
     * @param string|null $subAccountString Sub-account identifier (optional)
     * @param int|null $current Current page number (default 1)
     * @param int|null $size Page size (default 10, max 100)
     * @return array
     */
    public function getSubAccountList(
        ?string $subAccountString = null,
        ?int $current = null,
        ?int $size = null
    ): array {
        $params = [];
        
        if ($subAccountString !== null) $params['subAccountString'] = $subAccountString;
        if ($current !== null) $params['current'] = $current;
        if ($size !== null) $params['size'] = $size;

        return $this->client->request('GET', '/openApi/subAccount/v1/list', $params);
    }

    /**
     * Get sub-account assets
     * 
     * @param string $subUid Sub-account UID
     * @return array
     */
    public function getSubAccountAssets(string $subUid): array
    {
        return $this->client->request('GET', '/openApi/subAccount/v1/assets', [
            'subUid' => $subUid
        ]);
    }

    /**
     * Create sub-account API key
     * 
     * @param string $subAccountString Sub-account identifier
     * @param string $label API key label
     * @param array $permissions API key permissions
     * @param string|null $ip IP whitelist (optional)
     * @return array
     */
    public function createSubAccountApiKey(
        string $subAccountString,
        string $label,
        array $permissions,
        ?string $ip = null
    ): array {
        $params = [
            'subAccountString' => $subAccountString,
            'label' => $label,
            'permissions' => json_encode($permissions)
        ];
        
        if ($ip !== null) $params['ip'] = $ip;

        return $this->client->request('POST', '/openApi/subAccount/v1/apiKey/create', $params);
    }

    /**
     * Query API key information
     * 
     * @param string|null $subAccountString Sub-account identifier (optional)
     * @return array
     */
    public function queryApiKey(?string $subAccountString = null): array
    {
        $params = [];
        
        if ($subAccountString !== null) $params['subAccountString'] = $subAccountString;

        return $this->client->request('GET', '/openApi/account/v1/apiKey/query', $params);
    }

    /**
     * Edit sub-account API key
     * 
     * @param string $subAccountString Sub-account identifier
     * @param string $apiKey API key to edit
     * @param array $permissions New permissions
     * @param string|null $ip IP whitelist (optional)
     * @return array
     */
    public function editSubAccountApiKey(
        string $subAccountString,
        string $apiKey,
        array $permissions,
        ?string $ip = null
    ): array {
        $params = [
            'subAccountString' => $subAccountString,
            'apiKey' => $apiKey,
            'permissions' => json_encode($permissions)
        ];
        
        if ($ip !== null) $params['ip'] = $ip;

        return $this->client->request('POST', '/openApi/subAccount/v1/apiKey/edit', $params);
    }

    /**
     * Delete sub-account API key
     * 
     * @param string $subAccountString Sub-account identifier
     * @param string $apiKey API key to delete
     * @return array
     */
    public function deleteSubAccountApiKey(string $subAccountString, string $apiKey): array
    {
        return $this->client->request('POST', '/openApi/subAccount/v1/apiKey/del', [
            'subAccountString' => $subAccountString,
            'apiKey' => $apiKey
        ]);
    }

    /**
     * Update sub-account status
     * 
     * @param string $subAccountString Sub-account identifier
     * @param int $status Status (1: enable, 2: disable)
     * @return array
     */
    public function updateSubAccountStatus(string $subAccountString, int $status): array
    {
        return $this->client->request('POST', '/openApi/subAccount/v1/updateStatus', [
            'subAccountString' => $subAccountString,
            'status' => $status
        ]);
    }

    /**
     * Authorize sub-account for internal transfer
     * 
     * @param string $subAccountString Sub-account identifier
     * @param int $canTransfer Authorization status (1: authorize, 0: revoke)
     * @return array
     */
    public function authorizeSubAccountInternalTransfer(string $subAccountString, int $canTransfer): array
    {
        return $this->client->request('POST', '/openApi/account/v1/innerTransfer/authorizeSubAccount', [
            'subAccountString' => $subAccountString,
            'canTransfer' => $canTransfer
        ]);
    }

    /**
     * Sub-account internal transfer
     * 
     * @param string $coin Coin name
     * @param string $walletType Wallet type (SPOT, PERPETUAL)
     * @param float $amount Transfer amount
     * @param string $transferType Transfer type (FROM_MAIN_TO_SUB, FROM_SUB_TO_MAIN, FROM_SUB_TO_SUB)
     * @param string|null $fromSubUid Source sub-account UID (for FROM_SUB_TO_SUB)
     * @param string|null $toSubUid Target sub-account UID
     * @param string|null $clientId Client order ID
     * @return array
     */
    public function subAccountInternalTransfer(
        string $coin,
        string $walletType,
        float $amount,
        string $transferType,
        ?string $fromSubUid = null,
        ?string $toSubUid = null,
        ?string $clientId = null
    ): array {
        $params = [
            'coin' => $coin,
            'walletType' => $walletType,
            'amount' => $amount,
            'transferType' => $transferType
        ];
        
        if ($fromSubUid !== null) $params['fromSubUid'] = $fromSubUid;
        if ($toSubUid !== null) $params['toSubUid'] = $toSubUid;
        if ($clientId !== null) $params['clientId'] = $clientId;

        return $this->client->request('POST', '/openApi/wallets/v1/capital/subAccountInnerTransfer/apply', $params);
    }

    /**
     * Create sub-account deposit address
     * 
     * @param string $coin Coin name
     * @param string $network Network name
     * @param string $subUid Sub-account UID
     * @return array
     */
    public function createSubAccountDepositAddress(string $coin, string $network, string $subUid): array
    {
        return $this->client->request('POST', '/openApi/wallets/v1/capital/deposit/createSubAddress', [
            'coin' => $coin,
            'network' => $network,
            'subUid' => $subUid
        ]);
    }

    /**
     * Get sub-account deposit address
     * 
     * @param string $coin Coin name
     * @param string $subUid Sub-account UID
     * @param string|null $network Network name (optional)
     * @return array
     */
    public function getSubAccountDepositAddress(string $coin, string $subUid, ?string $network = null): array
    {
        $params = [
            'coin' => $coin,
            'subUid' => $subUid
        ];
        
        if ($network !== null) $params['network'] = $network;

        return $this->client->request('GET', '/openApi/wallets/v1/capital/subAccount/deposit/address', $params);
    }

    /**
     * Get sub-account deposit history
     * 
     * @param string $subUid Sub-account UID
     * @param string|null $coin Coin name (optional)
     * @param int|null $status Deposit status (optional)
     * @param int|null $startTime Start timestamp in milliseconds (optional)
     * @param int|null $endTime End timestamp in milliseconds (optional)
     * @param int|null $offset Offset for pagination (optional)
     * @param int|null $limit Number of records (default 1000, max 1000)
     * @return array
     */
    public function getSubAccountDepositHistory(
        string $subUid,
        ?string $coin = null,
        ?int $status = null,
        ?int $startTime = null,
        ?int $endTime = null,
        ?int $offset = null,
        ?int $limit = null
    ): array {
        $params = ['subUid' => $subUid];
        
        if ($coin !== null) $params['coin'] = $coin;
        if ($status !== null) $params['status'] = $status;
        if ($startTime !== null) $params['startTime'] = $startTime;
        if ($endTime !== null) $params['endTime'] = $endTime;
        if ($offset !== null) $params['offset'] = $offset;
        if ($limit !== null) $params['limit'] = $limit;

        return $this->client->request('GET', '/openApi/wallets/v1/capital/deposit/subHisrec', $params);
    }

    /**
     * Get sub-account internal transfer records
     * 
     * @param string|null $clientId Client order ID (optional)
     * @param int|null $startTime Start timestamp in milliseconds (optional)
     * @param int|null $endTime End timestamp in milliseconds (optional)
     * @param int|null $current Current page number (default 1)
     * @param int|null $size Page size (default 10, max 100)
     * @return array
     */
    public function getSubAccountInternalTransferRecords(
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

        return $this->client->request('GET', '/openApi/wallets/v1/capital/subAccount/innerTransfer/records', $params);
    }

    /**
     * Get sub-account asset transfer history
     * 
     * @param string $subUid Sub-account UID
     * @param string|null $type Transfer type (optional)
     * @param int|null $startTime Start timestamp in milliseconds (optional)
     * @param int|null $endTime End timestamp in milliseconds (optional)
     * @param int|null $current Current page number (default 1)
     * @param int|null $size Page size (default 10, max 100)
     * @return array
     */
    public function getSubAccountAssetTransferHistory(
        string $subUid,
        ?string $type = null,
        ?int $startTime = null,
        ?int $endTime = null,
        ?int $current = null,
        ?int $size = null
    ): array {
        $params = ['subUid' => $subUid];
        
        if ($type !== null) $params['type'] = $type;
        if ($startTime !== null) $params['startTime'] = $startTime;
        if ($endTime !== null) $params['endTime'] = $endTime;
        if ($current !== null) $params['current'] = $current;
        if ($size !== null) $params['size'] = $size;

        return $this->client->request('GET', '/openApi/account/transfer/v1/subAccount/asset/transferHistory', $params);
    }

    /**
     * Get supported coins for sub-account asset transfer
     * 
     * @param string $subUid Sub-account UID
     * @return array
     */
    public function getSubAccountTransferSupportedCoins(string $subUid): array
    {
        return $this->client->request('POST', '/openApi/account/transfer/v1/subAccount/transferAsset/supportCoins', [
            'subUid' => $subUid
        ]);
    }

    /**
     * Sub-account asset transfer
     * 
     * @param string $subUid Sub-account UID
     * @param string $type Transfer type
     * @param string $asset Asset name
     * @param float $amount Transfer amount
     * @return array
     */
    public function subAccountAssetTransfer(string $subUid, string $type, string $asset, float $amount): array
    {
        return $this->client->request('POST', '/openApi/account/transfer/v1/subAccount/transferAsset', [
            'subUid' => $subUid,
            'type' => $type,
            'asset' => $asset,
            'amount' => $amount
        ]);
    }

    /**
     * Get all sub-account balances
     * 
     * @param string|null $subAccountString Sub-account identifier (optional)
     * @return array
     */
    public function getAllSubAccountBalances(?string $subAccountString = null): array
    {
        $params = [];
        
        if ($subAccountString !== null) $params['subAccountString'] = $subAccountString;

        return $this->client->request('GET', '/openApi/subAccount/v1/allAccountBalance', $params);
    }
}
