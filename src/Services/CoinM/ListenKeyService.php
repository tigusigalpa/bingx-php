<?php

namespace Tigusigalpa\BingX\Services\CoinM;

use Tigusigalpa\BingX\Http\BaseHttpClient;

/**
 * Coin-M Perpetual Futures Listen Key Service
 * 
 * Manages WebSocket listen keys for Coin-M perpetual futures account data streams.
 * Listen keys are required for private WebSocket connections to receive:
 * - Account balance updates
 * - Position updates
 * - Order updates
 */
class ListenKeyService
{
    protected BaseHttpClient $client;

    public function __construct(BaseHttpClient $client)
    {
        $this->client = $client;
    }

    /**
     * Generate a new listen key
     * 
     * POST /openApi/cswap/v1/user/listen/key
     * 
     * Creates a new listen key for WebSocket authentication.
     * The key is valid for 60 minutes and must be extended periodically.
     * 
     * @return array Response with 'listenKey' field
     * 
     * @example
     * $response = $service->generate();
     * $listenKey = $response['listenKey'];
     * 
     * // Use the listen key for WebSocket connection
     * $wsUrl = "wss://open-api-swap.bingx.com/swap-market?listenKey={$listenKey}";
     */
    public function generate(): array
    {
        return $this->client->request('POST', '/openApi/cswap/v1/user/listen/key', []);
    }

    /**
     * Extend listen key validity
     * 
     * PUT /openApi/cswap/v1/user/listen/key
     * 
     * Extends the validity of an existing listen key by another 60 minutes.
     * Should be called every 30 minutes to keep the connection alive.
     * 
     * @param string $listenKey The listen key to extend
     * @return array Success response
     * 
     * @example
     * // Extend every 30 minutes
     * $service->extend($listenKey);
     */
    public function extend(string $listenKey): array
    {
        return $this->client->request('PUT', '/openApi/cswap/v1/user/listen/key', [
            'listenKey' => $listenKey
        ]);
    }

    /**
     * Delete listen key
     * 
     * DELETE /openApi/cswap/v1/user/listen/key
     * 
     * Invalidates a listen key and closes the associated WebSocket connection.
     * Should be called when closing the application or no longer need updates.
     * 
     * @param string $listenKey The listen key to delete
     * @return array Success response
     * 
     * @example
     * // Close connection and delete key
     * $service->delete($listenKey);
     */
    public function delete(string $listenKey): array
    {
        return $this->client->request('DELETE', '/openApi/cswap/v1/user/listen/key', [
            'listenKey' => $listenKey
        ]);
    }
}
