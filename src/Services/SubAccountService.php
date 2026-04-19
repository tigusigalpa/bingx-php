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
     * @param string $subAccountString Sub-account username (must start with letter, contain number, >6 chars)
     * @param string|null $note Optional remark/note for the sub-account
     * @return array Response containing subUid and subAccountString
     */
    public function createSubAccount(string $subAccountString, ?string $note = null): array
    {
        $params = ['subAccountString' => $subAccountString];
        if ($note !== null) {
            $params['note'] = $note;
        }
        
        return $this->client->request('POST', '/openApi/subAccount/v1/create', $params, true, 'json');
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
     * @param string $subUid Sub-account UID
     * @param string $note API key label/note
     * @param array $permissions API key permissions (1=Spot, 2=Read, 3=Perp Futures, 4=Universal Transfer, 5=Withdraw, 7=Internal Transfer)
     * @param array|null $ipAddresses Optional IP whitelist array
     * @return array Response containing apiKey and secretKey
     */
    public function createSubAccountApiKey(
        string $subUid,
        string $note,
        array $permissions,
        ?array $ipAddresses = null
    ): array {
        $params = [
            'subUid' => $subUid,
            'note' => $note,
            'permissions' => $permissions
        ];
        
        if ($ipAddresses !== null) {
            $params['ipAddresses'] = $ipAddresses;
        }

        return $this->client->request('POST', '/openApi/subAccount/v1/apiKey/create', $params, true, 'json');
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
     * @param string $subUid Sub-account UID
     * @param string $apiKey API key to edit
     * @param array $permissions New permissions (1=Spot, 2=Read, 3=Perp Futures, 4=Universal Transfer, 5=Withdraw, 7=Internal Transfer)
     * @param array|null $ipAddresses Optional IP whitelist array
     * @return array
     */
    public function editSubAccountApiKey(
        string $subUid,
        string $apiKey,
        array $permissions,
        ?array $ipAddresses = null
    ): array {
        $params = [
            'subUid' => $subUid,
            'apiKey' => $apiKey,
            'permissions' => $permissions
        ];
        
        if ($ipAddresses !== null) {
            $params['ipAddresses'] = $ipAddresses;
        }

        return $this->client->request('POST', '/openApi/subAccount/v1/apiKey/edit', $params, true, 'json');
    }

    /**
     * Delete sub-account API key
     * 
     * @param string $subUid Sub-account UID
     * @param string $apiKey API key to delete
     * @return array
     */
    public function deleteSubAccountApiKey(string $subUid, string $apiKey): array
    {
        return $this->client->request('POST', '/openApi/subAccount/v1/apiKey/del', [
            'subUid' => $subUid,
            'apiKey' => $apiKey
        ], true, 'json');
    }

    /**
     * Update sub-account status (freeze/unfreeze)
     * 
     * @param string $subUid Sub-account UID
     * @param bool $freeze True to freeze, false to unfreeze
     * @return array
     */
    public function updateSubAccountStatus(string $subUid, bool $freeze): array
    {
        return $this->client->request('POST', '/openApi/subAccount/v1/updateStatus', [
            'subUid' => $subUid,
            'freeze' => $freeze
        ], true, 'json');
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
     * @param int $walletType Wallet type: 1=Fund Account, 2=Standard Futures, 3=Perpetual Futures, 15=Spot Account
     * @param float $amount Transfer amount
     * @param int $userAccountType User account type: 1=UID, 2=Phone number, 3=Email
     * @param string $userAccount User account (UID, phone number, or email)
     * @param string|null $callingCode Phone area code (required when userAccountType=2)
     * @param string|null $transferClientId Client-defined internal transfer ID (alphanumeric, max 100 chars)
     * @param int|null $recvWindow Request validity time window in milliseconds
     * @return array
     */
    public function subAccountInternalTransfer(
        string $coin,
        int $walletType,
        float $amount,
        int $userAccountType,
        string $userAccount,
        ?string $callingCode = null,
        ?string $transferClientId = null,
        ?int $recvWindow = null
    ): array {
        $params = [
            'coin' => $coin,
            'walletType' => $walletType,
            'amount' => $amount,
            'userAccountType' => $userAccountType,
            'userAccount' => $userAccount
        ];
        
        if ($callingCode !== null) $params['callingCode'] = $callingCode;
        if ($transferClientId !== null) $params['transferClientId'] = $transferClientId;
        if ($recvWindow !== null) $params['recvWindow'] = $recvWindow;

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

    /**
     * Sub-Mother Account Asset Transfer Interface
     * 
     * Note: This endpoint is only available to the master account.
     * 
     * @param string $assetName Asset name (e.g., USDT)
     * @param float $transferAmount Transfer amount
     * @param int $fromUid Payer UID
     * @param int $fromType Payer sub-account type: 1=Parent account, 2=Sub-account
     * @param int $fromAccountType Payer account type: 1=Funding, 2=Standard futures, 3=Perpetual U-based, 15=Spot
     * @param int $toUid Receiver UID
     * @param int $toType Receiver sub-account type: 1=Parent account, 2=Sub-account
     * @param int $toAccountType Receiver account type: 1=Funding, 2=Standard futures, 3=Perpetual U-based, 15=Spot
     * @param string $remark Transfer remarks
     * @param int|null $recvWindow Execution window time (cannot exceed 60000)
     * @return array Response with tranId (transfer record ID)
     */
    public function subMotherAccountAssetTransfer(
        string $assetName,
        float $transferAmount,
        int $fromUid,
        int $fromType,
        int $fromAccountType,
        int $toUid,
        int $toType,
        int $toAccountType,
        string $remark,
        ?int $recvWindow = null
    ): array {
        $params = [
            'assetName' => $assetName,
            'transferAmount' => $transferAmount,
            'fromUid' => $fromUid,
            'fromType' => $fromType,
            'fromAccountType' => $fromAccountType,
            'toUid' => $toUid,
            'toType' => $toType,
            'toAccountType' => $toAccountType,
            'remark' => $remark
        ];
        
        if ($recvWindow !== null) $params['recvWindow'] = $recvWindow;

        return $this->client->request('POST', '/openApi/account/transfer/v1/subAccount/transferAsset', $params);
    }

    /**
     * Query Sub-Mother Account Transferable Amount
     * 
     * Note: This endpoint is only available to the master account.
     * 
     * @param int $fromUid Payer UID
     * @param int $fromAccountType Payer account type: 1=Funding, 2=Standard futures, 3=Perpetual U-Based
     * @param int $toUid Receiver UID
     * @param int $toAccountType Receiver account type: 1=Funding, 2=Standard futures, 3=Perpetual U-Based
     * @param int|null $recvWindow Execution window time (cannot exceed 60000)
     * @return array Response with coins array containing id, name, and availableAmount
     */
    public function getSubMotherAccountTransferableAmount(
        int $fromUid,
        int $fromAccountType,
        int $toUid,
        int $toAccountType,
        ?int $recvWindow = null
    ): array {
        $params = [
            'fromUid' => $fromUid,
            'fromAccountType' => $fromAccountType,
            'toUid' => $toUid,
            'toAccountType' => $toAccountType
        ];
        
        if ($recvWindow !== null) $params['recvWindow'] = $recvWindow;

        return $this->client->request('POST', '/openApi/account/transfer/v1/subAccount/transferAsset/supportCoins', $params);
    }

    /**
     * Query Sub-Mother Account Transfer History
     * 
     * Note: This endpoint is only available to the master account.
     * 
     * @param int $uid UID to query
     * @param string|null $type Transfer type filter (optional)
     * @param string|null $tranId Transfer ID (optional)
     * @param int|null $startTime Start time in milliseconds (optional)
     * @param int|null $endTime End time in milliseconds (optional)
     * @param int|null $pageId Current page (default 1)
     * @param int|null $pagingSize Page size (default 10, max 100)
     * @param int|null $recvWindow Execution window time (cannot exceed 60000)
     * @return array Response with total count and rows array
     */
    public function getSubMotherAccountTransferHistory(
        int $uid,
        ?string $type = null,
        ?string $tranId = null,
        ?int $startTime = null,
        ?int $endTime = null,
        ?int $pageId = null,
        ?int $pagingSize = null,
        ?int $recvWindow = null
    ): array {
        $params = ['uid' => $uid];
        
        if ($type !== null) $params['type'] = $type;
        if ($tranId !== null) $params['tranId'] = $tranId;
        if ($startTime !== null) $params['startTime'] = $startTime;
        if ($endTime !== null) $params['endTime'] = $endTime;
        if ($pageId !== null) $params['pageId'] = $pageId;
        if ($pagingSize !== null) $params['pagingSize'] = $pagingSize;
        if ($recvWindow !== null) $params['recvWindow'] = $recvWindow;

        return $this->client->request('GET', '/openApi/account/transfer/v1/subAccount/asset/transferHistory', $params);
    }
}
