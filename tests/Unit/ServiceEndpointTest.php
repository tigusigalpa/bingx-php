<?php

namespace Tigusigalpa\BingX\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Tigusigalpa\BingX\Http\BaseHttpClient;
use Tigusigalpa\BingX\Services\AccountService;
use Tigusigalpa\BingX\Services\MarketService;
use Tigusigalpa\BingX\Services\SpotOrderRequest;
use Tigusigalpa\BingX\Services\SpotTradeService;
use Tigusigalpa\BingX\Services\TradeService;
use Tigusigalpa\BingX\Services\TwapService;

class ServiceEndpointTest extends TestCase
{
    public function testValidatedSpotOrderKeepsDecimalStringsAndUsesSpotEndpoint(): void
    {
        $http = $this->createMock(BaseHttpClient::class);
        $http->expects($this->once())
            ->method('request')
            ->with(
                'POST',
                '/openApi/spot/v1/trade/order',
                [
                    'symbol' => 'BTC-USDT',
                    'side' => 'BUY',
                    'type' => 'LIMIT',
                    'quantity' => '0.001',
                    'price' => '50000.10',
                    'timeInForce' => 'GTC',
                ]
            )
            ->willReturn(['code' => 0]);

        $service = new SpotTradeService($http);
        $result = $service->createOrderRequest(new SpotOrderRequest(
            'BTC-USDT',
            SpotTradeService::SIDE_BUY,
            SpotTradeService::ORDER_TYPE_LIMIT,
            '0.001',
            '50000.10',
            null,
            SpotTradeService::TIME_IN_FORCE_GTC
        ));

        $this->assertSame(['code' => 0], $result);
    }

    public function testValidatedSpotOrderRejectsAZeroQuantity(): void
    {
        $service = new SpotTradeService($this->createMock(BaseHttpClient::class));

        $this->expectException(\InvalidArgumentException::class);
        $service->createOrderRequest(new SpotOrderRequest(
            'BTC-USDT',
            SpotTradeService::SIDE_BUY,
            SpotTradeService::ORDER_TYPE_LIMIT,
            '0',
            '50000'
        ));
    }

    public function testCorrectedSwapV2EndpointsAreUsed(): void
    {
        $http = $this->createMock(BaseHttpClient::class);
        $http->expects($this->exactly(4))
            ->method('request')
            ->willReturnCallback(function (string $method, string $path): array {
                static $expected = [
                    ['GET', '/openApi/swap/v2/user/positionRisk'],
                    ['GET', '/openApi/swap/v2/market/indexPrice'],
                    ['POST', '/openApi/swap/v2/trade/oneClickReversePosition'],
                    ['POST', '/openApi/swap/v2/trade/twapOrder'],
                ];

                $current = array_shift($expected);
                $this->assertSame($current, [$method, $path]);

                return ['code' => 0];
            });

        (new AccountService($http))->getPositionRisk('BTC-USDT');
        (new MarketService($http))->getIndexPrice('BTC-USDT');
        (new TradeService($http))->oneClickReversePosition('BTC-USDT');
        (new TwapService($http))->createOrder(['symbol' => 'BTC-USDT']);
    }
}
