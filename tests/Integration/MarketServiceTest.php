<?php

namespace Tigusigalpa\BingX\Tests\Integration;

use Tigusigalpa\BingX\Tests\TestCase;
use PHPUnit\Framework\Attributes\Group;

#[Group('safe')]
class MarketServiceTest extends TestCase
{
    public function testGetAllSymbols(): void
    {
        $response = $this->client->market()->getAllSymbols();
        
        $this->assertSuccessResponse($response);
        $this->assertArrayHasKey('spot', $response);
        $this->assertArrayHasKey('futures', $response);
        $this->assertIsArray($response['spot']);
        $this->assertIsArray($response['futures']);
        
        // Verify structure of symbol data
        if (!empty($response['spot'])) {
            $spotSymbol = $response['spot'][0];
            $this->assertArrayHasKey('symbol', $spotSymbol);
            $this->assertArrayHasKey('baseAsset', $spotSymbol);
            $this->assertArrayHasKey('quoteAsset', $spotSymbol);
        }
        
        if (!empty($response['futures'])) {
            $futureSymbol = $response['futures'][0];
            $this->assertArrayHasKey('symbol', $futureSymbol);
            $this->assertArrayHasKey('baseAsset', $futureSymbol);
            $this->assertArrayHasKey('quoteAsset', $futureSymbol);
        }
    }

    public function testGetSpotSymbols(): void
    {
        $response = $this->client->market()->getSpotSymbols();
        
        $this->assertSuccessResponse($response);
        $this->assertIsArray($response);
        
        if (!empty($response)) {
            $symbol = $response[0];
            $this->assertArrayHasKey('symbol', $symbol);
            $this->assertArrayHasKey('baseAsset', $symbol);
            $this->assertArrayHasKey('quoteAsset', $symbol);
            $this->assertArrayHasKey('status', $symbol);
        }
    }

    public function testGetFuturesSymbols(): void
    {
        $response = $this->client->market()->getFuturesSymbols();
        
        $this->assertSuccessResponse($response);
        $this->assertIsArray($response);
        
        if (!empty($response)) {
            $symbol = $response[0];
            $this->assertArrayHasKey('symbol', $symbol);
            $this->assertArrayHasKey('baseAsset', $symbol);
            $this->assertArrayHasKey('quoteAsset', $symbol);
            $this->assertArrayHasKey('contractType', $symbol);
        }
    }

    public function testGetLatestPrice(): void
    {
        $symbol = $this->config['test_symbol'];
        $response = $this->client->market()->getLatestPrice($symbol);
        
        $this->assertSuccessResponse($response);
        $this->assertArrayHasKey('symbol', $response);
        $this->assertEquals($symbol, $response['symbol']);
        $this->assertArrayHasKey('price', $response);
        $this->assertIsNumeric($response['price']);
    }

    public function testGetSpotLatestPrice(): void
    {
        $symbol = $this->config['test_symbol_spot'];
        $response = $this->client->market()->getSpotLatestPrice($symbol);
        
        $this->assertSuccessResponse($response);
        $this->assertArrayHasKey('symbol', $response);
        $this->assertEquals($symbol, $response['symbol']);
        $this->assertArrayHasKey('price', $response);
        $this->assertIsNumeric($response['price']);
    }

    public function testGetDepth(): void
    {
        $symbol = $this->config['test_symbol'];
        $limit = 20;
        
        $response = $this->client->market()->getDepth($symbol, $limit);
        
        $this->assertSuccessResponse($response);
        $this->assertArrayHasKey('symbol', $response);
        $this->assertEquals($symbol, $response['symbol']);
        $this->assertArrayHasKey('bids', $response);
        $this->assertArrayHasKey('asks', $response);
        $this->assertIsArray($response['bids']);
        $this->assertIsArray($response['asks']);
        
        // Verify depth structure
        if (!empty($response['bids'])) {
            $bid = $response['bids'][0];
            $this->assertCount(2, $bid); // [price, quantity]
            $this->assertIsNumeric($bid[0]);
            $this->assertIsNumeric($bid[1]);
        }
    }

    public function testGetSpotDepth(): void
    {
        $symbol = $this->config['test_symbol_spot'];
        $limit = 20;
        
        $response = $this->client->market()->getSpotDepth($symbol, $limit);
        
        $this->assertSuccessResponse($response);
        $this->assertArrayHasKey('symbol', $response);
        $this->assertEquals($symbol, $response['symbol']);
        $this->assertArrayHasKey('bids', $response);
        $this->assertArrayHasKey('asks', $response);
        $this->assertIsArray($response['bids']);
        $this->assertIsArray($response['asks']);
    }

    public function testGetKlines(): void
    {
        $symbol = $this->config['test_symbol'];
        $interval = '1h';
        $limit = 10;
        
        $response = $this->client->market()->getKlines($symbol, $interval, $limit);
        
        $this->assertSuccessResponse($response);
        $this->assertIsArray($response);
        
        if (!empty($response)) {
            $kline = $response[0];
            $this->assertCount(6, $kline); // [openTime, open, high, low, close, volume]
            $this->assertIsNumeric($kline[0]); // timestamp
            $this->assertIsNumeric($kline[1]); // open
            $this->assertIsNumeric($kline[2]); // high
            $this->assertIsNumeric($kline[3]); // low
            $this->assertIsNumeric($kline[4]); // close
            $this->assertIsNumeric($kline[5]); // volume
        }
    }

    public function testGetSpotKlines(): void
    {
        $symbol = $this->config['test_symbol_spot'];
        $interval = '1h';
        $limit = 10;
        
        $response = $this->client->market()->getSpotKlines($symbol, $interval, $limit);
        
        $this->assertSuccessResponse($response);
        $this->assertIsArray($response);
        
        if (!empty($response)) {
            $kline = $response[0];
            $this->assertCount(6, $kline);
            $this->assertIsNumeric($kline[0]);
            $this->assertIsNumeric($kline[1]);
            $this->assertIsNumeric($kline[2]);
            $this->assertIsNumeric($kline[3]);
            $this->assertIsNumeric($kline[4]);
            $this->assertIsNumeric($kline[5]);
        }
    }

    public function testGet24hrTicker(): void
    {
        $symbol = $this->config['test_symbol'];
        $response = $this->client->market()->get24hrTicker($symbol);
        
        $this->assertSuccessResponse($response);
        $this->assertArrayHasKey('symbol', $response);
        $this->assertEquals($symbol, $response['symbol']);
        $this->assertArrayHasKey('priceChange', $response);
        $this->assertArrayHasKey('priceChangePercent', $response);
        $this->assertArrayHasKey('lastPrice', $response);
        $this->assertIsNumeric($response['lastPrice']);
    }

    public function testGetSpot24hrTicker(): void
    {
        $symbol = $this->config['test_symbol_spot'];
        $response = $this->client->market()->getSpot24hrTicker($symbol);
        
        $this->assertSuccessResponse($response);
        $this->assertArrayHasKey('symbol', $response);
        $this->assertEquals($symbol, $response['symbol']);
        $this->assertArrayHasKey('priceChange', $response);
        $this->assertArrayHasKey('priceChangePercent', $response);
        $this->assertArrayHasKey('lastPrice', $response);
        $this->assertIsNumeric($response['lastPrice']);
    }

    public function testGetFundingRateHistory(): void
    {
        $symbol = $this->config['test_symbol'];
        $response = $this->client->market()->getFundingRateHistory($symbol);
        
        $this->assertSuccessResponse($response);
        $this->assertIsArray($response);
        
        if (!empty($response)) {
            $funding = $response[0];
            $this->assertArrayHasKey('symbol', $funding);
            $this->assertEquals($symbol, $funding['symbol']);
            $this->assertArrayHasKey('fundingRate', $funding);
            $this->assertArrayHasKey('fundingTime', $funding);
        }
    }

    public function testGetMarkPrice(): void
    {
        $symbol = $this->config['test_symbol'];
        $response = $this->client->market()->getMarkPrice($symbol);
        
        $this->assertSuccessResponse($response);
        $this->assertArrayHasKey('symbol', $response);
        $this->assertEquals($symbol, $response['symbol']);
        $this->assertArrayHasKey('markPrice', $response);
        $this->assertIsNumeric($response['markPrice']);
    }
}
