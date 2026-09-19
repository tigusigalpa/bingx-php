<?php

namespace Tigusigalpa\BingX\Tests\Unit;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Tigusigalpa\BingX\BingxClient;
use Tigusigalpa\BingX\CoinMClient;
use Tigusigalpa\BingX\Http\BaseHttpClient;
use Tigusigalpa\BingX\Services\AccountService;
use Tigusigalpa\BingX\Services\CoinM\ListenKeyService as CoinMListenKeyService;
use Tigusigalpa\BingX\Services\CoinM\MarketService as CoinMMarketService;
use Tigusigalpa\BingX\Services\CoinM\TradeService as CoinMTradeService;
use Tigusigalpa\BingX\Services\ContractService;
use Tigusigalpa\BingX\Services\CopyTradingService;
use Tigusigalpa\BingX\Services\ListenKeyService;
use Tigusigalpa\BingX\Services\MarketService;
use Tigusigalpa\BingX\Services\SpotAccountService;
use Tigusigalpa\BingX\Services\SpotTradeService;
use Tigusigalpa\BingX\Services\SubAccountService;
use Tigusigalpa\BingX\Services\TradeService;
use Tigusigalpa\BingX\Services\TwapService;
use Tigusigalpa\BingX\Services\WalletService;
use Tigusigalpa\BingX\TradFiClient;
use Tigusigalpa\BingX\WebSocket\AccountDataStream;
use Tigusigalpa\BingX\WebSocket\MarketDataStream;

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
        $this->assertInstanceOf(ContractService::class, $this->client->contract());
        $this->assertInstanceOf(ListenKeyService::class, $this->client->listenKey());
        $this->assertInstanceOf(WalletService::class, $this->client->wallet());
        $this->assertInstanceOf(SpotAccountService::class, $this->client->spotAccount());
        $this->assertInstanceOf(SubAccountService::class, $this->client->subAccount());
        $this->assertInstanceOf(CopyTradingService::class, $this->client->copyTrading());
        $this->assertInstanceOf(TwapService::class, $this->client->twap());
    }

    public function testLazyClientsAreCached(): void
    {
        $this->assertInstanceOf(CoinMClient::class, $this->client->coinM());
        $this->assertSame($this->client->coinM(), $this->client->coinM());

        $this->assertInstanceOf(TradFiClient::class, $this->client->tradFi());
        $this->assertSame($this->client->tradFi(), $this->client->tradFi());
        $this->assertInstanceOf(MarketService::class, $this->client->tradFi()->market());
        $this->assertInstanceOf(TradeService::class, $this->client->tradFi()->trade());
        $this->assertInstanceOf(AccountService::class, $this->client->tradFi()->account());
        $this->assertInstanceOf(ListenKeyService::class, $this->client->tradFi()->listenKey());
        $this->assertInstanceOf(CoinMMarketService::class, $this->client->coinM()->market());
        $this->assertInstanceOf(CoinMTradeService::class, $this->client->coinM()->trade());
        $this->assertInstanceOf(CoinMListenKeyService::class, $this->client->coinM()->listenKey());
        $this->assertSame($this->httpClient, $this->client->coinM()->getHttpClient());
    }

    public function testDemoClientUsesVstEndpoint(): void
    {
        $client = BingxClient::newDemoClient('key', 'secret');

        $this->assertSame('https://open-api-vst.bingx.com', $client->getEndpoint());
        $this->assertTrue($client->isDemo());
        $this->assertSame(BingxClient::ENVIRONMENT_DEMO, $client->getEnvironment());
    }

    public function testLiveClientReportsItsEnvironment(): void
    {
        $this->assertFalse($this->client->isDemo());
        $this->assertSame(BingxClient::ENVIRONMENT_LIVE, $this->client->getEnvironment());
    }

    public function testClientCreatesConfigurableWebSocketStreamsWithoutConnecting(): void
    {
        $market = $this->client->marketDataStream('wss://example.test/market');
        $account = $this->client->accountDataStream('listen key', 'wss://example.test/account');

        $this->assertInstanceOf(MarketDataStream::class, $market);
        $this->assertSame('wss://example.test/market', $market->getUrl());
        $this->assertInstanceOf(AccountDataStream::class, $account);
        $this->assertSame('wss://example.test/account?listenKey=listen%20key', $account->getUrl());
    }

    public function testClientExposesItsInjectedHttpClient(): void
    {
        $this->assertSame($this->httpClient, $this->client->getHttpClient());
    }

    public function testLegacyConvenienceMethodsDelegateToTheirServices(): void
    {
        $this->httpClient->expects($this->exactly(3))
            ->method('request')
            ->willReturn(['code' => 0, 'data' => []]);

        $this->assertSame(['code' => 0, 'data' => []], $this->client->getBalance());
        $this->assertSame(['code' => 0, 'data' => []], $this->client->getSymbols());
        $this->assertSame(['code' => 0, 'data' => []], $this->client->createOrder([
            'symbol' => 'BTC-USDT',
            'side' => 'BUY',
            'type' => 'MARKET',
            'quantity' => '0.001',
        ]));
    }
}
