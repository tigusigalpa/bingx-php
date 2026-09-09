<?php

namespace Tigusigalpa\BingX\Tests\Unit;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Tigusigalpa\BingX\Exceptions\AuthenticationException;
use Tigusigalpa\BingX\Http\BaseHttpClient;

class BaseHttpClientSignatureTest extends TestCase
{
    public function testGetSignsRawSortedParametersAndAppendsSignatureLast(): void
    {
        $history = [];
        $http = $this->recordingHttpClient($history);
        $client = new BaseHttpClient('key', 'secret', 'https://example.test', null, 'hex', $http);

        $client->request('GET', '/openApi/swap/v2/user/balance', [
            'symbol' => 'BTC-USDT',
            'timestamp' => 1700000000000,
        ]);

        $canonical = 'symbol=BTC-USDT&timestamp=1700000000000';
        $expected = $canonical . '&signature=' . hash_hmac('sha256', $canonical, 'secret');

        $this->assertSame($expected, $history[0]['request']->getUri()->getQuery());
        $this->assertFalse($history[0]['request']->hasHeader('X-SOURCE-KEY'));
    }

    public function testPostSignsRawJsonParameter(): void
    {
        $history = [];
        $http = $this->recordingHttpClient($history);
        $client = new BaseHttpClient('key', 'secret', 'https://example.test', null, 'hex', $http);

        $orders = [['symbol' => 'BTC-USDT', 'quantity' => '0.001']];
        $client->request('POST', '/openApi/swap/v2/trade/batchOrders', [
            'orders' => $orders,
            'timestamp' => 1700000000000,
        ]);

        $canonical = 'orders=[{"symbol":"BTC-USDT","quantity":"0.001"}]&timestamp=1700000000000';
        $expected = $canonical . '&signature=' . hash_hmac('sha256', $canonical, 'secret');

        $this->assertSame($expected, (string) $history[0]['request']->getBody());
        $this->assertSame('application/x-www-form-urlencoded', $history[0]['request']->getHeaderLine('Content-Type'));
    }

    public function testBase64RequiresAnExplicitOptIn(): void
    {
        $history = [];
        $http = $this->recordingHttpClient($history);
        $client = new BaseHttpClient('key', 'secret', 'https://example.test', null, 'base64', $http);

        $client->request('GET', '/endpoint', ['timestamp' => 1700000000000]);

        $expected = base64_encode(hash_hmac('sha256', 'timestamp=1700000000000', 'secret', true));
        $this->assertSame('timestamp=1700000000000&signature=' . urlencode($expected), $history[0]['request']->getUri()->getQuery());
    }

    public function testHttpApiErrorsAreMappedWithoutMaskingTheOriginalException(): void
    {
        $http = new Client([
            'handler' => HandlerStack::create(new MockHandler([
                new Response(401, [], '{"code":100412,"msg":"Invalid API key"}'),
            ])),
        ]);
        $client = new BaseHttpClient('key', 'secret', 'https://example.test', null, 'hex', $http);

        $this->expectException(AuthenticationException::class);
        $this->expectExceptionMessage('Invalid API key');
        $client->request('GET', '/openApi/swap/v2/user/balance', ['timestamp' => 1700000000000]);
    }

    /**
     * @param array<int, array<string, mixed>> $history
     */
    private function recordingHttpClient(array &$history): Client
    {
        $stack = HandlerStack::create(new MockHandler([
            new Response(200, [], '{"code":0,"msg":"success"}'),
        ]));
        $stack->push(Middleware::history($history));

        return new Client(['handler' => $stack]);
    }
}
