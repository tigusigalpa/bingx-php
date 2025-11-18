<?php

namespace Tigusigalpa\BingX\Tests\Integration;

use Tigusigalpa\BingX\Tests\TestCase;
use PHPUnit\Framework\Attributes\Group;

class TradeServiceTest extends TestCase
{
    #[Group('safe')]
    public function testGetOpenOrders(): void
    {
        $response = $this->client->trade()->getOpenOrders();
        
        $this->assertSuccessResponse($response);
        $this->assertIsArray($response);
        
        if (!empty($response)) {
            $order = $response[0];
            $this->assertArrayHasKey('symbol', $order);
            $this->assertArrayHasKey('orderId', $order);
            $this->assertArrayHasKey('side', $order);
            $this->assertArrayHasKey('type', $order);
            $this->assertArrayHasKey('origQty', $order);
        }
    }

    #[Group('safe')]
    public function testGetOpenOrdersForSymbol(): void
    {
        $symbol = $this->config['test_symbol'];
        $response = $this->client->trade()->getOpenOrders($symbol);
        
        $this->assertSuccessResponse($response);
        $this->assertIsArray($response);
        
        // If orders exist, they should be for the requested symbol
        if (!empty($response)) {
            foreach ($response as $order) {
                $this->assertEquals($symbol, $order['symbol']);
            }
        }
    }

    #[Group('safe')]
    public function testGetOrderHistory(): void
    {
        $symbol = $this->config['test_symbol'];
        $limit = 10;
        
        $response = $this->client->trade()->getOrderHistory($symbol, $limit);
        
        $this->assertSuccessResponse($response);
        $this->assertIsArray($response);
        $this->assertLessThanOrEqual($limit, count($response));
        
        if (!empty($response)) {
            $order = $response[0];
            $this->assertArrayHasKey('symbol', $order);
            $this->assertEquals($symbol, $order['symbol']);
            $this->assertArrayHasKey('orderId', $order);
            $this->assertArrayHasKey('side', $order);
            $this->assertArrayHasKey('type', $order);
        }
    }

    #[Group('safe')]
    public function testGetFilledOrders(): void
    {
        $symbol = $this->config['test_symbol'];
        $limit = 10;
        
        $response = $this->client->trade()->getFilledOrders($symbol, $limit);
        
        $this->assertSuccessResponse($response);
        $this->assertIsArray($response);
        $this->assertLessThanOrEqual($limit, count($response));
        
        if (!empty($response)) {
            $order = $response[0];
            $this->assertArrayHasKey('symbol', $order);
            $this->assertEquals($symbol, $order['symbol']);
            $this->assertArrayHasKey('orderId', $order);
            $this->assertArrayHasKey('status', $order);
            $this->assertEquals('FILLED', $order['status']);
        }
    }

    #[Group('safe')]
    public function testGetUserTrades(): void
    {
        $symbol = $this->config['test_symbol'];
        $limit = 10;
        
        $response = $this->client->trade()->getUserTrades($symbol, $limit);
        
        $this->assertSuccessResponse($response);
        $this->assertIsArray($response);
        $this->assertLessThanOrEqual($limit, count($response));
        
        if (!empty($response)) {
            $trade = $response[0];
            $this->assertArrayHasKey('symbol', $trade);
            $this->assertEquals($symbol, $trade['symbol']);
            $this->assertArrayHasKey('id', $trade);
            $this->assertArrayHasKey('price', $trade);
            $this->assertArrayHasKey('qty', $trade);
            $this->assertArrayHasKey('time', $trade);
            $this->assertIsNumeric($trade['price']);
            $this->assertIsNumeric($trade['qty']);
        }
    }

    #[Group('safe')]
    public function testGetOrder(): void
    {
        // This test requires a valid order ID, so we'll skip it by default
        $this->markTestSkipped('Requires a valid order ID - run manually with actual orderId');
        
        $symbol = $this->config['test_symbol'];
        $orderId = '123456789'; // Replace with actual order ID
        
        $response = $this->client->trade()->getOrder($symbol, $orderId);
        
        $this->assertSuccessResponse($response);
        $this->assertArrayHasKey('symbol', $response);
        $this->assertEquals($symbol, $response['symbol']);
        $this->assertArrayHasKey('orderId', $response);
        $this->assertEquals($orderId, $response['orderId']);
    }

    #[Group('dangerous')]
    public function testCreateSpotMarketOrder(): void
    {
        $this->markTestSkipped('Skipping dangerous test: creates real market order');
        
        $symbol = $this->config['test_symbol_spot'];
        $quantity = $this->config['test_quantity'];
        
        $response = $this->client->trade()->spotMarketBuy($symbol, $quantity);
        
        $this->assertSuccessResponse($response);
        $this->assertArrayHasKey('symbol', $response);
        $this->assertEquals($symbol, $response['symbol']);
        $this->assertArrayHasKey('orderId', $response);
        $this->assertArrayHasKey('side', $response);
        $this->assertEquals('BUY', $response['side']);
        $this->assertArrayHasKey('type', $response);
        $this->assertEquals('MARKET', $response['type']);
    }

    #[Group('dangerous')]
    public function testCreateSpotLimitOrder(): void
    {
        $this->markTestSkipped('Skipping dangerous test: creates real limit order');
        
        $symbol = $this->config['test_symbol_spot'];
        $quantity = $this->config['test_quantity'];
        $price = 50000; // Adjust based on current market price
        
        $response = $this->client->trade()->spotLimitBuy($symbol, $quantity, $price);
        
        $this->assertSuccessResponse($response);
        $this->assertArrayHasKey('symbol', $response);
        $this->assertEquals($symbol, $response['symbol']);
        $this->assertArrayHasKey('orderId', $response);
        $this->assertArrayHasKey('price', $response);
        $this->assertEquals($price, $response['price']);
        $this->assertArrayHasKey('type', $response);
        $this->assertEquals('LIMIT', $response['type']);
    }

    #[Group('dangerous')]
    public function testCreateFuturesMarketOrder(): void
    {
        $this->markTestSkipped('Skipping dangerous test: creates real futures market order');
        
        $symbol = $this->config['test_symbol'];
        $margin = $this->config['test_margin'];
        $leverage = $this->config['test_leverage'];
        
        $response = $this->client->trade()->futuresLongMarket($symbol, $margin, $leverage);
        
        $this->assertSuccessResponse($response);
        $this->assertArrayHasKey('symbol', $response);
        $this->assertEquals($symbol, $response['symbol']);
        $this->assertArrayHasKey('orderId', $response);
        $this->assertArrayHasKey('side', $response);
        $this->assertEquals('BUY', $response['side']);
        $this->assertArrayHasKey('positionSide', $response);
        $this->assertEquals('LONG', $response['positionSide']);
    }

    #[Group('dangerous')]
    public function testCreateTestOrder(): void
    {
        // Test orders are safe - they don't execute in real market
        $symbol = $this->config['test_symbol_spot'];
        $quantity = $this->config['test_quantity'];
        
        $response = $this->client->trade()->createTestOrder([
            'symbol' => $symbol,
            'side' => 'BUY',
            'type' => 'MARKET',
            'quantity' => $quantity
        ]);
        
        $this->assertSuccessResponse($response);
        $this->assertArrayHasKey('symbol', $response);
        $this->assertEquals($symbol, $response['symbol']);
        // Test orders should not have real orderId
        $this->assertArrayHasKey('orderId', $response);
    }

    #[Group('dangerous')]
    public function testCancelOrder(): void
    {
        $this->markTestSkipped('Skipping dangerous test: cancels real order');
        
        $symbol = $this->config['test_symbol'];
        $orderId = '123456789'; // Replace with actual order ID
        
        $response = $this->client->trade()->cancelOrder($symbol, $orderId);
        
        $this->assertSuccessResponse($response);
        $this->assertArrayHasKey('symbol', $response);
        $this->assertEquals($symbol, $response['symbol']);
        $this->assertArrayHasKey('orderId', $response);
        $this->assertEquals($orderId, $response['orderId']);
    }

    #[Group('dangerous')]
    public function testCancelAllOrders(): void
    {
        $this->markTestSkipped('Skipping dangerous test: cancels all orders for symbol');
        
        $symbol = $this->config['test_symbol'];
        
        $response = $this->client->trade()->cancelAllOrders($symbol);
        
        $this->assertSuccessResponse($response);
        $this->assertArrayHasKey('symbol', $response);
        $this->assertEquals($symbol, $response['symbol']);
    }

    #[Group('dangerous')]
    public function testCancelBatchOrders(): void
    {
        $this->markTestSkipped('Skipping dangerous test: cancels multiple orders');
        
        $symbol = $this->config['test_symbol'];
        $orderIds = ['123456789', '987654321']; // Replace with actual order IDs
        
        $response = $this->client->trade()->cancelBatchOrders($symbol, $orderIds);
        
        $this->assertSuccessResponse($response);
        $this->assertArrayHasKey('symbol', $response);
        $this->assertEquals($symbol, $response['symbol']);
    }

    #[Group('dangerous')]
    public function testOrderBuilder(): void
    {
        $this->markTestSkipped('Skipping dangerous test: OrderBuilder creates real orders');
        
        $symbol = $this->config['test_symbol_spot'];
        $quantity = $this->config['test_quantity'];
        
        $response = $this->client->trade()->order()
            ->spot()
            ->symbol($symbol)
            ->buy()
            ->type('MARKET')
            ->quantity($quantity)
            ->test() // Make it a test order for safety
            ->execute();
        
        $this->assertSuccessResponse($response);
        $this->assertArrayHasKey('symbol', $response);
        $this->assertEquals($symbol, $response['symbol']);
        $this->assertArrayHasKey('side', $response);
        $this->assertEquals('BUY', $response['side']);
    }

    #[Group('safe')]
    public function testCalculateFuturesCommission(): void
    {
        $margin = 100.0;
        $leverage = 10;
        
        $response = $this->client->trade()->calculateFuturesCommission($margin, $leverage);
        
        $this->assertIsArray($response);
        $this->assertArrayHasKey('margin', $response);
        $this->assertArrayHasKey('leverage', $response);
        $this->assertArrayHasKey('position_value', $response);
        $this->assertArrayHasKey('commission_rate', $response);
        $this->assertArrayHasKey('commission_rate_percent', $response);
        $this->assertArrayHasKey('commission', $response);
        $this->assertArrayHasKey('commission_rounded', $response);
        $this->assertArrayHasKey('net_position_value', $response);
        
        $this->assertEquals($margin, $response['margin']);
        $this->assertEquals($leverage, $response['leverage']);
        $this->assertEquals($margin * $leverage, $response['position_value']);
        $this->assertEquals(0.00045, $response['commission_rate']); // 0.045%
        $this->assertEquals(0.045, $response['commission_rate_percent']);
        $this->assertEquals(0.45, $response['commission']);
        $this->assertEquals(999.55, $response['net_position_value']);
    }

    #[Group('safe')]
    public function testCalculateBatchCommission(): void
    {
        $orders = [
            ['margin' => 100, 'leverage' => 10],
            ['margin' => 200, 'leverage' => 5],
            ['margin' => 150, 'leverage' => 20]
        ];
        
        $response = $this->client->trade()->calculateBatchCommission($orders);
        
        $this->assertIsArray($response);
        $this->assertArrayHasKey('commission_rate', $response);
        $this->assertArrayHasKey('total_commission', $response);
        $this->assertArrayHasKey('orders_count', $response);
        $this->assertArrayHasKey('orders', $response);
        
        $this->assertEquals(0.00045, $response['commission_rate']);
        $this->assertEquals(3, $response['orders_count']);
        $this->assertEquals(0.45 + 0.45 + 1.35, $response['total_commission']); // 2.25
        $this->assertCount(3, $response['orders']);
    }

    #[Group('safe')]
    public function testGetCommissionRates(): void
    {
        $response = $this->client->trade()->getCommissionRates();
        
        $this->assertIsArray($response);
        $this->assertArrayHasKey('futures_standard', $response);
        $this->assertArrayHasKey('futures_standard_formula', $response);
        
        $this->assertEquals(0.00045, $response['futures_standard']['rate']);
        $this->assertEquals(0.045, $response['futures_standard']['percent']);
        $this->assertEquals('margin × leverage × 0.045%', $response['futures_standard_formula']);
    }

    #[Group('safe')]
    public function testGetCommissionAmount(): void
    {
        $margin = 100.0;
        $leverage = 10;
        
        $commission = $this->client->trade()->getCommissionAmount($margin, $leverage);
        
        $this->assertIsFloat($commission);
        $this->assertEquals(0.45, $commission);
    }

    #[Group('dangerous')]
    public function testChangeLeverage(): void
    {
        $this->markTestSkipped('Skipping dangerous test: changeLeverage modifies account settings');
        
        $symbol = $this->config['test_symbol'];
        $side = 'BOTH';
        $leverage = $this->config['test_leverage'];
        
        $response = $this->client->trade()->changeLeverage($symbol, $side, $leverage);
        
        $this->assertSuccessResponse($response);
        $this->assertArrayHasKey('symbol', $response);
        $this->assertEquals($symbol, $response['symbol']);
        $this->assertArrayHasKey('leverage', $response);
        $this->assertEquals($leverage, $response['leverage']);
    }
}
