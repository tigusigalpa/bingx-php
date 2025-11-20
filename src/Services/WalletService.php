<?php

namespace Tigusigalpa\BingX\Services;

use Tigusigalpa\BingX\Http\BaseHttpClient;

class WalletService
{
    protected BaseHttpClient $client;

    public function __construct(BaseHttpClient $client)
    {
        $this->client = $client;
    }

    /**
     * Get deposit history
     * 
     * @param string|null $coin Coin name
     * @param int|null $status Deposit status (0: pending, 6: credited but cannot withdraw, 1: success)
     * @param int|null $startTime Start timestamp in milliseconds
     * @param int|null $endTime End timestamp in milliseconds
     * @param int|null $offset Offset for pagination
     * @param int|null $limit Number of records (default 1000, max 1000)
     * @return array
     */
    public function getDepositHistory(
        ?string $coin = null,
        ?int $status = null,
        ?int $startTime = null,
        ?int $endTime = null,
        ?int $offset = null,
        ?int $limit = null
    ): array {
        $params = [];
        
        if ($coin !== null) $params['coin'] = $coin;
        if ($status !== null) $params['status'] = $status;
        if ($startTime !== null) $params['startTime'] = $startTime;
        if ($endTime !== null) $params['endTime'] = $endTime;
        if ($offset !== null) $params['offset'] = $offset;
        if ($limit !== null) $params['limit'] = $limit;

        return $this->client->request('GET', '/openApi/api/v3/capital/deposit/hisrec', $params);
    }

    /**
     * Get withdrawal history
     * 
     * @param string|null $coin Coin name
     * @param string|null $withdrawOrderId Withdrawal order ID
     * @param int|null $status Withdrawal status (0: Email Sent, 1: Cancelled, 2: Awaiting Approval, 3: Rejected, 4: Processing, 5: Failure, 6: Completed)
     * @param int|null $startTime Start timestamp in milliseconds
     * @param int|null $endTime End timestamp in milliseconds
     * @param int|null $offset Offset for pagination
     * @param int|null $limit Number of records (default 1000, max 1000)
     * @return array
     */
    public function getWithdrawalHistory(
        ?string $coin = null,
        ?string $withdrawOrderId = null,
        ?int $status = null,
        ?int $startTime = null,
        ?int $endTime = null,
        ?int $offset = null,
        ?int $limit = null
    ): array {
        $params = [];
        
        if ($coin !== null) $params['coin'] = $coin;
        if ($withdrawOrderId !== null) $params['withdrawOrderId'] = $withdrawOrderId;
        if ($status !== null) $params['status'] = $status;
        if ($startTime !== null) $params['startTime'] = $startTime;
        if ($endTime !== null) $params['endTime'] = $endTime;
        if ($offset !== null) $params['offset'] = $offset;
        if ($limit !== null) $params['limit'] = $limit;

        return $this->client->request('GET', '/openApi/api/v3/capital/withdraw/history', $params);
    }

    /**
     * Get all coin information
     * 
     * @return array
     */
    public function getAllCoinInfo(): array
    {
        return $this->client->request('GET', '/openApi/wallets/v1/capital/config/getall');
    }

    /**
     * Get main account deposit address
     * 
     * @param string $coin Coin name
     * @param string|null $network Network name
     * @return array
     */
    public function getDepositAddress(string $coin, ?string $network = null): array
    {
        $params = ['coin' => $coin];
        
        if ($network !== null) {
            $params['network'] = $network;
        }

        return $this->client->request('GET', '/openApi/wallets/v1/capital/deposit/address', $params);
    }

    /**
     * Get deposit risk control records
     * 
     * @param string|null $coin Coin name
     * @param int|null $startTime Start timestamp in milliseconds
     * @param int|null $endTime End timestamp in milliseconds
     * @param int|null $offset Offset for pagination
     * @param int|null $limit Number of records (default 1000, max 1000)
     * @return array
     */
    public function getDepositRiskRecords(
        ?string $coin = null,
        ?int $startTime = null,
        ?int $endTime = null,
        ?int $offset = null,
        ?int $limit = null
    ): array {
        $params = [];
        
        if ($coin !== null) $params['coin'] = $coin;
        if ($startTime !== null) $params['startTime'] = $startTime;
        if ($endTime !== null) $params['endTime'] = $endTime;
        if ($offset !== null) $params['offset'] = $offset;
        if ($limit !== null) $params['limit'] = $limit;

        return $this->client->request('GET', '/openApi/wallets/v1/capital/deposit/riskRecords', $params);
    }

    /**
     * Withdraw
     * 
     * @param string $coin Coin name
     * @param string $address Withdrawal address
     * @param float $amount Withdrawal amount
     * @param string|null $network Network name
     * @param string|null $addressTag Address tag (for coins like XRP, XMR)
     * @param string|null $walletType Wallet type (0: spot wallet, 1: fund wallet)
     * @return array
     */
    public function withdraw(
        string $coin,
        string $address,
        float $amount,
        ?string $network = null,
        ?string $addressTag = null,
        ?string $walletType = null
    ): array {
        $params = [
            'coin' => $coin,
            'address' => $address,
            'amount' => $amount
        ];
        
        if ($network !== null) $params['network'] = $network;
        if ($addressTag !== null) $params['addressTag'] = $addressTag;
        if ($walletType !== null) $params['walletType'] = $walletType;

        return $this->client->request('POST', '/openApi/wallets/v1/capital/withdraw/apply', $params);
    }
}
