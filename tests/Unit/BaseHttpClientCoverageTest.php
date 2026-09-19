<?php

namespace Tigusigalpa\BingX\Tests\Unit;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Tigusigalpa\BingX\Exceptions\ApiException;
use Tigusigalpa\BingX\Exceptions\InsufficientBalanceException;
use Tigusigalpa\BingX\Exceptions\RateLimitException;
use Tigusigalpa\BingX\Http\BaseHttpClient;

class BaseHttpClientCoverageTest extends TestCase
{
    public function testUnsignedGetAndJsonPostUseTheExpectedTransportModes(): void
    {
        $history = [];
        $client = $this->clientWithResponses($history, [
            new Response(200, [], '{"code":0,"data":[]}'),
            new Response(200, [], '{"code":0,"data":[]}'),
        ]);

        $client->request('GET', '/openApi/market/test', ['symbol' => 'BTC-USDT'], false);
        $client->request('POST', '/openApi/trade/test', ['symbol' => 'BTC-USDT'], true, 'json');

        $this->assertSame('symbol=BTC-USDT', $history[0]['request']->getUri()->getQuery());
        $this->assertSame('application/json', $history[1]['request']->getHeaderLine('Content-Type'));
        $body = json_decode((string) $history[1]['request']->getBody(), true);
        $this->assertSame('BTC-USDT', $body['symbol']);
        $this->assertArrayHasKey('timestamp', $body);
        $this->assertArrayHasKey('signature', $body);
    }

    public function testVstFallsBackToTheProHostOnlyAfterANetworkFailure(): void
    {
        $history = [];
        $handler = new MockHandler([
            new ConnectException('connection failed', new Request('GET', BaseHttpClient::DEMO_BASE_URI)),
            new Response(200, [], '{"code":0,"data":[]}'),
        ]);
        $stack = HandlerStack::create($handler);
        $stack->push(Middleware::history($history));
        $http = new Client(['handler' => $stack]);
        $client = new BaseHttpClient('key', 'secret', BaseHttpClient::DEMO_BASE_URI, null, 'hex', $http);

        $response = $client->request('GET', '/openApi/swap/v2/user/balance');

        $this->assertSame(['code' => 0, 'data' => []], $response);
        $this->assertTrue($client->isDemo());
        $this->assertCount(2, $history);
        $this->assertStringStartsWith(BaseHttpClient::DEMO_BASE_URI, (string) $history[0]['request']->getUri());
        $this->assertStringStartsWith('https://open-api-vst.bingx.pro', (string) $history[1]['request']->getUri());
    }

    public function testCurrentApiErrorsUseDedicatedExceptions(): void
    {
        $this->assertApiException(
            new Response(400, [], '{"code":100429,"msg":"Too many requests"}'),
            RateLimitException::class
        );
        $this->assertApiException(
            new Response(400, [], '{"code":200002,"msg":"Not enough funds"}'),
            InsufficientBalanceException::class
        );
        $this->assertApiException(
            new Response(400, [], '{"code":999999,"msg":"Unexpected API error"}'),
            ApiException::class
        );
    }

    public function testInvalidJsonResponseIsRejected(): void
    {
        $history = [];
        $client = $this->clientWithResponses($history, [new Response(200, [], 'not-json')]);

        $this->expectException(\Tigusigalpa\BingX\Exceptions\BingxException::class);
        $this->expectExceptionMessage('Invalid JSON response');
        $client->request('GET', '/openApi/test');
    }

    /**
     * @param array<int, array<string, mixed>> $history
     * @param array<int, Response> $responses
     */
    private function clientWithResponses(array &$history, array $responses): BaseHttpClient
    {
        $stack = HandlerStack::create(new MockHandler($responses));
        $stack->push(Middleware::history($history));

        return new BaseHttpClient(
            'key',
            'secret',
            'https://example.test',
            'source-key',
            'hex',
            new Client(['handler' => $stack])
        );
    }

    /** @param class-string<\Throwable> $exception */
    private function assertApiException(Response $response, string $exception): void
    {
        $history = [];
        $client = $this->clientWithResponses($history, [$response]);

        try {
            $client->request('GET', '/openApi/test');
            $this->fail('Expected API request to fail.');
        } catch (\Throwable $caught) {
            $this->assertInstanceOf($exception, $caught);
        }
    }
}
