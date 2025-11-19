<?php

namespace Tigusigalpa\BingX\WebSocket;

use WebSocket\Client;
use WebSocket\ConnectionException;

/**
 * Base WebSocket Client
 * 
 * Handles WebSocket connections to BingX API
 */
class WebSocketClient
{
    protected string $url;
    protected ?Client $client = null;
    protected array $callbacks = [];
    protected bool $running = false;

    public function __construct(string $url)
    {
        $this->url = $url;
    }

    /**
     * Connect to WebSocket server
     * 
     * @return void
     * @throws ConnectionException
     */
    public function connect(): void
    {
        $this->client = new Client($this->url, [
            'timeout' => 60,
            'persistent' => true,
        ]);
    }

    /**
     * Disconnect from WebSocket server
     * 
     * @return void
     */
    public function disconnect(): void
    {
        if ($this->client) {
            $this->client->close();
            $this->client = null;
        }
        $this->running = false;
    }

    /**
     * Send a message to the WebSocket server
     * 
     * @param array $message Message to send
     * @return void
     */
    public function send(array $message): void
    {
        if (!$this->client) {
            throw new \RuntimeException('WebSocket client is not connected');
        }
        
        $this->client->text(json_encode($message));
    }

    /**
     * Subscribe to a data stream
     * 
     * @param string $id Unique request ID
     * @param string $dataType Data type to subscribe to (e.g., 'BTC-USDT@trade')
     * @return void
     */
    public function subscribe(string $id, string $dataType): void
    {
        $this->send([
            'id' => $id,
            'reqType' => 'sub',
            'dataType' => $dataType,
        ]);
    }

    /**
     * Unsubscribe from a data stream
     * 
     * @param string $id Unique request ID
     * @param string $dataType Data type to unsubscribe from
     * @return void
     */
    public function unsubscribe(string $id, string $dataType): void
    {
        $this->send([
            'id' => $id,
            'reqType' => 'unsub',
            'dataType' => $dataType,
        ]);
    }

    /**
     * Register a callback for incoming messages
     * 
     * @param callable $callback Callback function to handle messages
     * @return void
     */
    public function onMessage(callable $callback): void
    {
        $this->callbacks[] = $callback;
    }

    /**
     * Start listening for messages
     * 
     * This method blocks until disconnect() is called
     * 
     * @return void
     */
    public function listen(): void
    {
        if (!$this->client) {
            throw new \RuntimeException('WebSocket client is not connected');
        }

        $this->running = true;

        while ($this->running) {
            try {
                $message = $this->client->receive();
                
                if ($message === null) {
                    continue;
                }

                // Decompress if gzipped
                $data = $this->decompressMessage($message);
                
                // Parse JSON
                $parsed = json_decode($data, true);
                
                if ($parsed === null) {
                    continue;
                }

                // Handle ping/pong
                if (isset($parsed['ping'])) {
                    $this->send(['pong' => $parsed['ping']]);
                    continue;
                }

                // Call registered callbacks
                foreach ($this->callbacks as $callback) {
                    call_user_func($callback, $parsed);
                }
                
            } catch (ConnectionException $e) {
                // Connection closed
                $this->running = false;
                break;
            } catch (\Exception $e) {
                // Log error but continue listening
                error_log('WebSocket error: ' . $e->getMessage());
            }
        }
    }

    /**
     * Decompress gzipped message
     * 
     * @param string $message Compressed message
     * @return string Decompressed message
     */
    protected function decompressMessage(string $message): string
    {
        // Check if message is gzipped
        if (substr($message, 0, 2) === "\x1f\x8b") {
            return gzdecode($message);
        }
        
        return $message;
    }

    /**
     * Check if client is connected
     * 
     * @return bool
     */
    public function isConnected(): bool
    {
        return $this->client !== null;
    }

    /**
     * Stop listening for messages
     * 
     * @return void
     */
    public function stop(): void
    {
        $this->running = false;
    }
}
