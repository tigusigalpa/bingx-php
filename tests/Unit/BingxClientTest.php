<?php

namespace Tigusigalpa\BingX\Tests\Unit;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Tigusigalpa\BingX\BingxClient;
use Tigusigalpa\BingX\Http\BaseHttpClient;
use Tigusigalpa\BingX\Services\MarketService;
use Tigusigalpa\BingX\Services\AccountService;
use Tigusigalpa\BingX\Services\TradeService;

class BingxClientTest extends TestCase
{
    private MockObject $httpClient;
    private BingxClient $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->httpClient = $this->createMock(BaseHttpClient::class);
        $this->client = new BingxClient($this->httpClient);
    }

    public function testClientReturnsMarketService(): void
    {
        $market = $this->client->market();
        
        $this->assertInstanceOf(MarketService::class, $market);
        $this->assertSame($market, $this->client->market()); // Test singleton
    }

    public function testClientReturnsAccountService(): void
    {
        $account = $this->client->account();
        
        $this->assertInstanceOf(AccountService::class, $account);
        $this->assertSame($account, $this->client->account()); // Test singleton
    }

    public function testClientReturnsTradeService(): void
    {
        $trade = $this->client->trade();
        
        $this->assertInstanceOf(TradeService::class, $trade);
        $this->assertSame($trade, $this->client->trade()); // Test singleton
    }

    public function testServicesShareHttpClient(): void
    {
        $market = $this->client->market();
        $account = $this->client->account();
        $trade = $this->client->trade();
        
        // All services should share the same HTTP client
        $this->assertEquals($market, $account);
        $this->assertEquals($account, $trade);
    }

    public function testClientConstructorAcceptsHttpClient(): void
    {
        $client = new BingxClient($this->httpClient);
        
        $this->assertInstanceOf(BingxClient::class, $client);
    }
}
