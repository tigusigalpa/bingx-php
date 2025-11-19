<?php

namespace Tigusigalpa\BingX\WebSocket;

/**
 * Account Data Stream
 * 
 * Provides WebSocket streaming for private account data
 * Requires a valid listen key
 */
class AccountDataStream extends WebSocketClient
{
    const BASE_URL = 'wss://open-api-swap.bingx.com/swap-market';

    /**
     * Create account data stream with listen key
     * 
     * @param string $listenKey Valid listen key from ListenKeyService
     */
    public function __construct(string $listenKey)
    {
        $url = self::BASE_URL . '?listenKey=' . $listenKey;
        parent::__construct($url);
    }

    /**
     * Process incoming account update messages
     * 
     * Automatically handles different event types:
     * - ACCOUNT_UPDATE: Balance and position updates
     * - ORDER_TRADE_UPDATE: Order status updates
     * 
     * @param callable $callback Callback function to handle account updates
     * @return void
     */
    public function onAccountUpdate(callable $callback): void
    {
        $this->onMessage(function ($data) use ($callback) {
            if (isset($data['e'])) {
                $eventType = $data['e'];
                
                switch ($eventType) {
                    case 'ACCOUNT_UPDATE':
                        // Balance and position update
                        call_user_func($callback, 'account', $data);
                        break;
                        
                    case 'ORDER_TRADE_UPDATE':
                        // Order status update
                        call_user_func($callback, 'order', $data);
                        break;
                        
                    default:
                        // Unknown event type
                        call_user_func($callback, 'unknown', $data);
                        break;
                }
            }
        });
    }

    /**
     * Listen for balance updates only
     * 
     * @param callable $callback Callback function to handle balance updates
     * @return void
     */
    public function onBalanceUpdate(callable $callback): void
    {
        $this->onMessage(function ($data) use ($callback) {
            if (isset($data['e']) && $data['e'] === 'ACCOUNT_UPDATE') {
                if (isset($data['a']['B'])) {
                    // Balance data
                    call_user_func($callback, $data['a']['B']);
                }
            }
        });
    }

    /**
     * Listen for position updates only
     * 
     * @param callable $callback Callback function to handle position updates
     * @return void
     */
    public function onPositionUpdate(callable $callback): void
    {
        $this->onMessage(function ($data) use ($callback) {
            if (isset($data['e']) && $data['e'] === 'ACCOUNT_UPDATE') {
                if (isset($data['a']['P'])) {
                    // Position data
                    call_user_func($callback, $data['a']['P']);
                }
            }
        });
    }

    /**
     * Listen for order updates only
     * 
     * @param callable $callback Callback function to handle order updates
     * @return void
     */
    public function onOrderUpdate(callable $callback): void
    {
        $this->onMessage(function ($data) use ($callback) {
            if (isset($data['e']) && $data['e'] === 'ORDER_TRADE_UPDATE') {
                if (isset($data['o'])) {
                    // Order data
                    call_user_func($callback, $data['o']);
                }
            }
        });
    }
}
