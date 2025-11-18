<?php

namespace Tigusigalpa\BingX\Tests\Integration;

use Tigusigalpa\BingX\Tests\TestCase;
use PHPUnit\Framework\Attributes\Group;

#[Group('safe')]
class AccountServiceTest extends TestCase
{
    public function testGetBalance(): void
    {
        $response = $this->client->account()->getBalance();
        
        $this->assertSuccessResponse($response);
        $this->assertArrayHasKey('balance', $response);
        $this->assertIsArray($response['balance']);
        
        if (!empty($response['balance'])) {
            $asset = $response['balance'][0];
            $this->assertArrayHasKey('asset', $asset);
            $this->assertArrayHasKey('free', $asset);
            $this->assertArrayHasKey('locked', $asset);
            $this->assertIsNumeric($asset['free']);
            $this->assertIsNumeric($asset['locked']);
        }
    }

    public function testGetPositions(): void
    {
        $response = $this->client->account()->getPositions();
        
        $this->assertSuccessResponse($response);
        $this->assertIsArray($response);
        
        if (!empty($response)) {
            $position = $response[0];
            $this->assertArrayHasKey('symbol', $position);
            $this->assertArrayHasKey('positionSide', $position);
            $this->assertArrayHasKey('positionAmt', $position);
            $this->assertArrayHasKey('entryPrice', $position);
            $this->assertArrayHasKey('markPrice', $position);
            $this->assertIsNumeric($position['positionAmt']);
            $this->assertIsNumeric($position['entryPrice']);
        }
    }

    public function testGetPositionsForSymbol(): void
    {
        $symbol = $this->config['test_symbol'];
        $response = $this->client->account()->getPositions($symbol);
        
        $this->assertSuccessResponse($response);
        $this->assertIsArray($response);
        
        // If positions exist, they should be for the requested symbol
        if (!empty($response)) {
            foreach ($response as $position) {
                $this->assertEquals($symbol, $position['symbol']);
            }
        }
    }

    public function testGetAccountInfo(): void
    {
        $response = $this->client->account()->getAccountInfo();
        
        $this->assertSuccessResponse($response);
        $this->assertArrayHasKey('feeTier', $response);
        $this->assertArrayHasKey('canTrade', $response);
        $this->assertArrayHasKey('canDeposit', $response);
        $this->assertArrayHasKey('canWithdraw', $response);
        $this->assertIsBool($response['canTrade']);
        $this->assertIsBool($response['canDeposit']);
        $this->assertIsBool($response['canWithdraw']);
    }

    public function testGetTradingFees(): void
    {
        $symbol = $this->config['test_symbol'];
        $response = $this->client->account()->getTradingFees($symbol);
        
        $this->assertSuccessResponse($response);
        $this->assertArrayHasKey('symbol', $response);
        $this->assertEquals($symbol, $response['symbol']);
        $this->assertArrayHasKey('makerCommission', $response);
        $this->assertArrayHasKey('takerCommission', $response);
        $this->assertIsNumeric($response['makerCommission']);
        $this->assertIsNumeric($response['takerCommission']);
    }

    public function testGetMarginMode(): void
    {
        $symbol = $this->config['test_symbol'];
        $response = $this->client->account()->getMarginMode($symbol);
        
        $this->assertSuccessResponse($response);
        $this->assertArrayHasKey('symbol', $response);
        $this->assertEquals($symbol, $response['symbol']);
        $this->assertArrayHasKey('marginMode', $response);
        $this->assertContains($response['marginMode'], ['ISOLATED', 'CROSSED']);
    }

    #[Group('dangerous')]
    public function testSetMarginMode(): void
    {
        $this->markTestSkipped('Skipping dangerous test: setMarginMode changes account settings');
        
        $symbol = $this->config['test_symbol'];
        $marginMode = 'ISOLATED';
        
        $response = $this->client->account()->setMarginMode($symbol, $marginMode);
        
        $this->assertSuccessResponse($response);
        $this->assertArrayHasKey('symbol', $response);
        $this->assertEquals($symbol, $response['symbol']);
        $this->assertArrayHasKey('marginMode', $response);
        $this->assertEquals($marginMode, $response['marginMode']);
    }

    public function testGetLeverage(): void
    {
        $symbol = $this->config['test_symbol'];
        $response = $this->client->account()->getLeverage($symbol);
        
        $this->assertSuccessResponse($response);
        $this->assertArrayHasKey('symbol', $response);
        $this->assertEquals($symbol, $response['symbol']);
        $this->assertArrayHasKey('leverage', $response);
        $this->assertIsNumeric($response['leverage']);
        $this->assertGreaterThanOrEqual(1, $response['leverage']);
        $this->assertLessThanOrEqual(125, $response['leverage']);
    }

    public function testGetLeverageWithRecvWindow(): void
    {
        $symbol = $this->config['test_symbol'];
        $recvWindow = 5000;
        
        $response = $this->client->account()->getLeverage($symbol, $recvWindow);
        
        $this->assertSuccessResponse($response);
        $this->assertArrayHasKey('symbol', $response);
        $this->assertEquals($symbol, $response['symbol']);
        $this->assertArrayHasKey('leverage', $response);
        $this->assertIsNumeric($response['leverage']);
    }

    #[Group('dangerous')]
    public function testSetLeverage(): void
    {
        $this->markTestSkipped('Skipping dangerous test: setLeverage changes account settings');
        
        $symbol = $this->config['test_symbol'];
        $side = 'BOTH';
        $leverage = $this->config['test_leverage'];
        
        $response = $this->client->account()->setLeverage($symbol, $side, $leverage);
        
        $this->assertSuccessResponse($response);
        $this->assertArrayHasKey('symbol', $response);
        $this->assertEquals($symbol, $response['symbol']);
        $this->assertArrayHasKey('leverage', $response);
        $this->assertEquals($leverage, $response['leverage']);
    }

    #[Group('dangerous')]
    public function testSetLeverageWithRecvWindow(): void
    {
        $this->markTestSkipped('Skipping dangerous test: setLeverage changes account settings');
        
        $symbol = $this->config['test_symbol'];
        $side = 'LONG';
        $leverage = $this->config['test_leverage'];
        $recvWindow = 5000;
        
        $response = $this->client->account()->setLeverage($symbol, $side, $leverage, $recvWindow);
        
        $this->assertSuccessResponse($response);
        $this->assertArrayHasKey('symbol', $response);
        $this->assertEquals($symbol, $response['symbol']);
        $this->assertArrayHasKey('leverage', $response);
        $this->assertEquals($leverage, $response['leverage']);
    }

    public function testGetPositionMargin(): void
    {
        $symbol = $this->config['test_symbol'];
        $response = $this->client->account()->getPositionMargin($symbol);
        
        $this->assertSuccessResponse($response);
        $this->assertArrayHasKey('symbol', $response);
        $this->assertEquals($symbol, $response['symbol']);
        $this->assertArrayHasKey('positionMargin', $response);
        $this->assertIsNumeric($response['positionMargin']);
    }

    #[Group('dangerous')]
    public function testSetPositionMargin(): void
    {
        $this->markTestSkipped('Skipping dangerous test: setPositionMargin modifies account funds');
        
        $symbol = $this->config['test_symbol'];
        $positionSide = 'BOTH';
        $amount = 10.0;
        $type = 1; // ADD
        
        $response = $this->client->account()->setPositionMargin($symbol, $positionSide, $amount, $type);
        
        $this->assertSuccessResponse($response);
        $this->assertArrayHasKey('symbol', $response);
        $this->assertEquals($symbol, $response['symbol']);
        $this->assertArrayHasKey('amount', $response);
        $this->assertEquals($amount, $response['amount']);
    }

    public function testGetBalanceHistory(): void
    {
        $coin = 'USDT';
        $limit = 10;
        
        $response = $this->client->account()->getBalanceHistory($coin, $limit);
        
        $this->assertSuccessResponse($response);
        $this->assertIsArray($response);
        
        if (!empty($response)) {
            $record = $response[0];
            $this->assertArrayHasKey('asset', $record);
            $this->assertEquals($coin, $record['asset']);
            $this->assertArrayHasKey('amount', $record);
            $this->assertArrayHasKey('type', $record);
            $this->assertArrayHasKey('timestamp', $record);
            $this->assertIsNumeric($record['amount']);
        }
    }
}
