<?php

namespace Tigusigalpa\BingX\Services;

use Tigusigalpa\BingX\Http\BaseHttpClient;

/**
 * Standard Contract Interface Service
 * 
 * Provides methods for querying standard contract positions, orders, and balance
 */
class ContractService
{
    protected BaseHttpClient $client;

    public function __construct(BaseHttpClient $client)
    {
        $this->client = $client;
    }

    /**
     * Query all positions for standard contracts
     * 
     * @param int|null $timestamp Request timestamp in milliseconds
     * @param int|null $recvWindow Request validity window in milliseconds
     * @return array
     */
    public function getAllPositions(
        ?int $timestamp = null,
        ?int $recvWindow = null
    ): array {
        $params = [
            'timestamp' => $timestamp ?? (int) (microtime(true) * 1000),
        ];
        
        if ($recvWindow !== null) {
            $params['recvWindow'] = $recvWindow;
        }
        
        return $this->client->request('GET', '/openApi/contract/v1/allPosition', $params);
    }

    /**
     * Query all historical orders for standard contracts
     * 
     * @param string $symbol Trading symbol (e.g., 'BTC-USDT')
     * @param int|null $orderId Order ID to query from
     * @param int|null $startTime Start time in milliseconds
     * @param int|null $endTime End time in milliseconds
     * @param int|null $limit Number of results to return (default: 500, max: 1000)
     * @param int|null $timestamp Request timestamp in milliseconds
     * @param int|null $recvWindow Request validity window in milliseconds
     * @return array
     */
    public function getAllOrders(
        string $symbol,
        ?int $orderId = null,
        ?int $startTime = null,
        ?int $endTime = null,
        ?int $limit = null,
        ?int $timestamp = null,
        ?int $recvWindow = null
    ): array {
        $params = [
            'symbol'    => $symbol,
            'timestamp' => $timestamp ?? (int) (microtime(true) * 1000),
        ];
        
        if ($orderId !== null) {
            $params['orderId'] = $orderId;
        }
        
        if ($startTime !== null) {
            $params['startTime'] = $startTime;
        }
        
        if ($endTime !== null) {
            $params['endTime'] = $endTime;
        }
        
        if ($limit !== null) {
            if ($limit < 1 || $limit > 1000) {
                throw new \InvalidArgumentException('Limit must be between 1 and 1000');
            }
            $params['limit'] = $limit;
        }
        
        if ($recvWindow !== null) {
            $params['recvWindow'] = $recvWindow;
        }
        
        return $this->client->request('GET', '/openApi/contract/v1/allOrders', $params);
    }

    /**
     * Query standard contract account balance
     * 
     * @param int|null $timestamp Request timestamp in milliseconds
     * @param int|null $recvWindow Request validity window in milliseconds
     * @return array
     */
    public function getBalance(
        ?int $timestamp = null,
        ?int $recvWindow = null
    ): array {
        $params = [
            'timestamp' => $timestamp ?? (int) (microtime(true) * 1000),
        ];
        
        if ($recvWindow !== null) {
            $params['recvWindow'] = $recvWindow;
        }
        
        return $this->client->request('GET', '/openApi/contract/v1/balance', $params);
    }
}
