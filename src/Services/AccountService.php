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
}
