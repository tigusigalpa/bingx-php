<?php

namespace Tigusigalpa\BingX\Tests\Unit;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Tigusigalpa\BingX\BingxClient;
use Tigusigalpa\BingX\CoinMClient;
use Tigusigalpa\BingX\Http\BaseHttpClient;
use Tigusigalpa\BingX\Services\AccountService;
use Tigusigalpa\BingX\Services\MarketService;
use Tigusigalpa\BingX\Services\SpotTradeService;
use Tigusigalpa\BingX\Services\TradeService;
use Tigusigalpa\BingX\TradFiClient;

class BingxClientTest extends TestCase
{
    private MockObject $httpClient;
    private BingxClient $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->httpClient = $this->createMock(BaseHttpClient::class);
        $this->client = new BingxClient('key', 'secret', 'https://example.test', null, 'hex', $this->httpClient);
    }

    public function testClientReturnsTheExpectedSingletonServices(): void
    {
        $this->assertInstanceOf(MarketService::class, $this->client->market());
        $this->assertInstanceOf(AccountService::class, $this->client->account());
        $this->assertInstanceOf(TradeService::class, $this->client->trade());
        $this->assertInstanceOf(SpotTradeService::class, $this->client->spotTrade());
        $this->assertSame($this->client->market(), $this->client->market());
        $this->assertSame($this->client->spotTrade(), $this->client->spotTrade());
    }

    public function testLazyClientsAreCached(): void
    {
        $this->assertInstanceOf(CoinMClient::class, $this->client->coinM());
        $this->assertSame($this->client->coinM(), $this->client->coinM());

        $this->assertInstanceOf(TradFiClient::class, $this->client->tradFi());
        $this->assertSame($this->client->tradFi(), $this->client->tradFi());
    }

    public function testDemoClientUsesVstEndpoint(): void
    {
        $client = BingxClient::newDemoClient('key', 'secret');

        $this->assertSame('https://open-api-vst.bingx.com', $client->getEndpoint());
    }

    public function testClientExposesItsInjectedHttpClient(): void
    {
        $this->assertSame($this->httpClient, $this->client->getHttpClient());
    }
}
