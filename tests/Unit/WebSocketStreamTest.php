<?php

namespace Tigusigalpa\BingX\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Tigusigalpa\BingX\WebSocket\AccountDataStream;
use Tigusigalpa\BingX\WebSocket\MarketDataStream;
use Tigusigalpa\BingX\WebSocket\WebSocketClient;
use WebSocket\Client as NativeWebSocketClient;

class WebSocketStreamTest extends TestCase
{
    public function testMarketSubscriptionsProduceTheExpectedMessages(): void
    {
        $stream = new CapturingMarketDataStream();

        $stream->subscribeTrade('BTC-USDT', 'trade');
        $stream->subscribeKline('BTC-USDT', '1m', 'kline');
        $stream->subscribeDepth('BTC-USDT', 20, 'depth');
        $stream->subscribeTicker('BTC-USDT', 'ticker');
        $stream->subscribeBookTicker('BTC-USDT', 'book');
        $stream->unsubscribeTrade('BTC-USDT', 'untrade');
        $stream->unsubscribeKline('BTC-USDT', '1m', 'unkline');
        $stream->unsubscribeDepth('BTC-USDT', 20, 'undepth');
        $stream->unsubscribeTicker('BTC-USDT', 'unticker');
        $stream->unsubscribeBookTicker('BTC-USDT', 'unbook');

        $this->assertCount(10, $stream->sent);
        $this->assertSame(['id' => 'trade', 'reqType' => 'sub', 'dataType' => 'BTC-USDT@trade'], $stream->sent[0]);
        $this->assertSame(['id' => 'unbook', 'reqType' => 'unsub', 'dataType' => 'BTC-USDT@bookTicker'], $stream->sent[9]);
        $this->assertFalse($stream->isConnected());
        $this->assertSame(MarketDataStream::URL, $stream->getUrl());
    }

    public function testAccountCallbacksClassifyEverySupportedEvent(): void
    {
        $stream = new AccountDataStream('listen key');
        $events = [];
        $balances = [];
        $positions = [];
        $orders = [];

        $stream->onAccountUpdate(function (string $type, array $data) use (&$events): void {
            $events[] = [$type, $data];
        });
        $stream->onBalanceUpdate(function (array $data) use (&$balances): void {
            $balances[] = $data;
        });
        $stream->onPositionUpdate(function (array $data) use (&$positions): void {
            $positions[] = $data;
        });
        $stream->onOrderUpdate(function (array $data) use (&$orders): void {
            $orders[] = $data;
        });

        $callbacks = $this->callbacksFor($stream);
        foreach ($callbacks as $callback) {
            $callback(['e' => 'ACCOUNT_UPDATE', 'a' => ['B' => ['USDT'], 'P' => ['BTC-USDT']]]);
            $callback(['e' => 'ORDER_TRADE_UPDATE', 'o' => ['orderId' => '1']]);
            $callback(['e' => 'UNKNOWN_EVENT']);
        }

        $this->assertSame('account', $events[0][0]);
        $this->assertSame('order', $events[1][0]);
        $this->assertSame('unknown', $events[2][0]);
        $this->assertSame([['USDT']], $balances);
        $this->assertSame([['BTC-USDT']], $positions);
        $this->assertSame([['orderId' => '1']], $orders);
        $this->assertSame('wss://open-api-swap.bingx.com/swap-market?listenKey=listen%20key', $stream->getUrl());
    }

    public function testBaseStreamHandlesPingAndStopsAfterAnIncomingMessage(): void
    {
        $stream = new WebSocketClient('wss://example.test/stream');
        $nativeClient = $this->createMock(NativeWebSocketClient::class);
        $nativeClient->expects($this->exactly(2))
            ->method('receive')
            ->willReturn('{"ping":"123"}', '{"event":"message"}');
        $nativeClient->expects($this->once())
            ->method('text')
            ->with('{"pong":"123"}');
        $this->setNativeClient($stream, $nativeClient);

        $received = [];
        $stream->onMessage(function (array $data) use (&$received, $stream): void {
            $received[] = $data;
            $stream->stop();
        });
        $stream->listen();

        $this->assertSame([['event' => 'message']], $received);
        $this->assertTrue($stream->isConnected());
    }

    public function testUnconnectedStreamCannotSendAndCanBeStoppedOrDisconnected(): void
    {
        $stream = new WebSocketClient('wss://example.test/stream');

        try {
            $stream->send(['id' => '1']);
            $this->fail('An unconnected stream must not send messages.');
        } catch (\RuntimeException $exception) {
            $this->assertSame('WebSocket client is not connected', $exception->getMessage());
        }

        $stream->stop();
        $stream->disconnect();
        $this->assertFalse($stream->isConnected());
    }

    /** @return callable[] */
    private function callbacksFor(WebSocketClient $stream): array
    {
        $property = new \ReflectionProperty(WebSocketClient::class, 'callbacks');
        $property->setAccessible(true);

        return $property->getValue($stream);
    }

    private function setNativeClient(WebSocketClient $stream, NativeWebSocketClient $client): void
    {
        $property = new \ReflectionProperty(WebSocketClient::class, 'client');
        $property->setAccessible(true);
        $property->setValue($stream, $client);
    }
}

class CapturingMarketDataStream extends MarketDataStream
{
    /** @var array<int, array<string, string>> */
    public array $sent = [];

    public function send(array $message): void
    {
        $this->sent[] = $message;
    }
}
