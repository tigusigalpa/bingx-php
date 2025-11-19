<?php

namespace Tigusigalpa\BingX\Services;

use Tigusigalpa\BingX\Http\BaseHttpClient;

/**
 * Listen Key Service
 * 
 * Manages listen keys for WebSocket user data streams
 */
class ListenKeyService
{
    protected BaseHttpClient $client;

    public function __construct(BaseHttpClient $client)
    {
        $this->client = $client;
    }

    /**
     * Generate a new listen key for user data stream
     * 
     * Listen keys are valid for 60 minutes and should be extended periodically
     * 
     * @param int|null $timestamp Request timestamp in milliseconds
     * @param int|null $recvWindow Request validity window in milliseconds
     * @return array Response containing listenKey
     */
    public function generate(
        ?int $timestamp = null,
        ?int $recvWindow = null
    ): array {
        $params = [
            'timestamp' => $timestamp ?? (int) (microtime(true) * 1000),
        ];
        
        if ($recvWindow !== null) {
            $params['recvWindow'] = $recvWindow;
        }
        
        return $this->client->request('POST', '/openApi/user/auth/userDataStream', $params);
    }

    /**
     * Extend the validity period of a listen key
     * 
     * Recommended to call this method every 30 minutes to keep the connection alive
     * 
     * @param string $listenKey The listen key to extend
     * @param int|null $timestamp Request timestamp in milliseconds
     * @param int|null $recvWindow Request validity window in milliseconds
     * @return array
     */
    public function extend(
        string $listenKey,
        ?int $timestamp = null,
        ?int $recvWindow = null
    ): array {
        $params = [
            'listenKey' => $listenKey,
            'timestamp' => $timestamp ?? (int) (microtime(true) * 1000),
        ];
        
        if ($recvWindow !== null) {
            $params['recvWindow'] = $recvWindow;
        }
        
        return $this->client->request('PUT', '/openApi/user/auth/userDataStream', $params);
    }

    /**
     * Delete a listen key
     * 
     * @param string $listenKey The listen key to delete
     * @param int|null $timestamp Request timestamp in milliseconds
     * @param int|null $recvWindow Request validity window in milliseconds
     * @return array
     */
    public function delete(
        string $listenKey,
        ?int $timestamp = null,
        ?int $recvWindow = null
    ): array {
        $params = [
            'listenKey' => $listenKey,
            'timestamp' => $timestamp ?? (int) (microtime(true) * 1000),
        ];
        
        if ($recvWindow !== null) {
            $params['recvWindow'] = $recvWindow;
        }
        
        return $this->client->request('DELETE', '/openApi/user/auth/userDataStream', $params);
    }
}
