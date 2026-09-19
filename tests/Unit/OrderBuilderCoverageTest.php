<?php

namespace Tigusigalpa\BingX\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Tigusigalpa\BingX\Builder\OrderBuilder;
use Tigusigalpa\BingX\Services\TradeService;
use Tigusigalpa\BingX\Tests\Support\RecordingHttpClient;

class OrderBuilderCoverageTest extends TestCase
{
    public function testCommissionHelpersReturnConsistentLocalCalculations(): void
    {
        $trade = new TradeService(new RecordingHttpClient());

        $single = $trade->calculateFuturesCommission(100.0, 10, 0.001);
        $batch = $trade->calculateBatchCommission([
            ['margin' => 100.0, 'leverage' => 10],
            ['margin' => 50.0, 'leverage' => 5],
        ]);

        $this->assertSame(1.0, $single['commission']);
        $this->assertSame(2, $batch['orders_count']);
        $this->assertSame(0.5625, $batch['total_commission']);
        $this->assertSame(0.45, $trade->getCommissionAmount(100.0, 10));
        $this->assertSame(0.00045, $trade->getCommissionRates()['futures_standard']['rate']);
        $this->assertInstanceOf(OrderBuilder::class, $trade->order());
    }

    public function testFuturesTestOrderDispatchesLeverageAndPreservesAdvancedFields(): void
    {
        $http = new RecordingHttpClient();
        $result = (new OrderBuilder(new TradeService($http)))
            ->futures()
            ->symbol('BTC-USDT')
            ->type('LIMIT')
            ->buy()
            ->long()
            ->leverage(10)
            ->quantity(0.01)
            ->margin(50.0)
            ->price(50000.0)
            ->stopLoss(49000.0)
            ->stopLossPercent(5.0)
            ->takeProfit(51000.0)
            ->takeProfitPercent(10.0)
            ->clientOrderId('coverage-order')
            ->timeInForce('GTC')
            ->reduceOnly(false)
            ->closePosition(false)
            ->stopPrice(49900.0)
            ->stopGuaranteed()
            ->priceRate(1.0)
            ->workingType('MARK_PRICE')
            ->newOrderRespType('RESULT')
            ->positionId(1001)
            ->timestamp(1700000000000)
            ->recvWindow(5000)
            ->activationPrice(49800.0)
            ->callbackRate(1.0)
            ->trailingStopPercent(1.0)
            ->takeProfitPrice(51000.0)
            ->stopLossPrice(49000.0)
            ->test()
            ->execute();

        $this->assertSame(['code' => 0, 'data' => []], $result);
        $this->assertCount(2, $http->requests);
        $this->assertSame('/openApi/swap/v2/trade/leverage', $http->requests[0]['path']);
        $this->assertSame('/openApi/swap/v2/trade/order/test', $http->requests[1]['path']);
        $this->assertEqualsWithDelta(47500.0, $http->requests[1]['params']['stopLoss'], 0.000001);
        $this->assertEqualsWithDelta(55000.0, $http->requests[1]['params']['takeProfit'], 0.000001);
    }

    public function testSpotOrderUsesTheRegularOrderEndpoint(): void
    {
        $http = new RecordingHttpClient();
        $data = (new OrderBuilder(new TradeService($http)))
            ->spot()
            ->symbol('BTC-USDT')
            ->type('MARKET')
            ->sell()
            ->quantity(0.01)
            ->clientOrderId('spot-coverage')
            ->timeInForce('GTC')
            ->getOrderData();

        $this->assertSame('SELL', $data['side']);
        $this->assertSame('MARKET', $data['type']);

        (new OrderBuilder(new TradeService($http)))
            ->spot()
            ->symbol('BTC-USDT')
            ->type('MARKET')
            ->buy()
            ->quantity(0.01)
            ->execute();

        $this->assertSame('/openApi/swap/v2/trade/order', $http->requests[0]['path']);
    }

    public function testSellPercentagesAreCalculatedFromTheLimitPrice(): void
    {
        $data = (new OrderBuilder(new TradeService(new RecordingHttpClient())))
            ->symbol('BTC-USDT')
            ->type('LIMIT')
            ->sell()
            ->short()
            ->quantity(1.0)
            ->price(100.0)
            ->stopLossPercent(5.0)
            ->takeProfitPercent(10.0)
            ->getOrderData();

        $this->assertSame(105.0, $data['stopLoss']);
        $this->assertSame(90.0, $data['takeProfit']);
    }

    public function testInvalidBuilderStatesProduceUsefulErrors(): void
    {
        $builder = (new OrderBuilder(new TradeService(new RecordingHttpClient())))
            ->spot()
            ->type('NOT_AN_ORDER')
            ->long()
            ->short()
            ->leverage(0)
            ->quantity(0.0)
            ->margin(0.0)
            ->price(0.0)
            ->stopLoss(0.0)
            ->stopLossPercent(101.0)
            ->takeProfit(0.0)
            ->takeProfitPercent(1001.0)
            ->stopPrice(0.0)
            ->priceRate(0.0)
            ->positionId(0)
            ->recvWindow(0)
            ->activationPrice(0.0)
            ->callbackRate(101.0)
            ->trailingStopPercent(101.0)
            ->takeProfitPrice(0.0)
            ->stopLossPrice(0.0)
            ->reduceOnly()
            ->closePosition();

        $this->assertFalse($builder->isValid());
        $this->assertNotEmpty($builder->getErrors());
        $this->expectException(\InvalidArgumentException::class);
        $builder->execute();
    }
}
