<?php

namespace Tigusigalpa\BingX\Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;
use Tigusigalpa\BingX\BingxClient;
use Tigusigalpa\BingX\Http\BaseHttpClient;

abstract class TestCase extends BaseTestCase
{
    protected BingxClient $client;
    protected array $config;

    protected function setUp(): void
    {
        parent::setUp();

        $this->config = [
            'api_key' => $_ENV['BINGX_API_KEY'] ?? '',
            'api_secret' => $_ENV['BINGX_API_SECRET'] ?? '',
            'base_uri' => $_ENV['BINGX_BASE_URI'] ?? 'https://open-api.bingx.com',
            'test_symbol' => $_ENV['BINGX_TEST_SYMBOL'] ?? 'BTC-USDT',
            'test_symbol_spot' => $_ENV['BINGX_TEST_SYMBOL_SPOT'] ?? 'BTC-USDT',
            'test_leverage' => (int) ($_ENV['BINGX_TEST_LEVERAGE'] ?? 10),
            'test_margin' => (float) ($_ENV['BINGX_TEST_MARGIN'] ?? 100),
            'test_quantity' => (float) ($_ENV['BINGX_TEST_QUANTITY'] ?? 0.001),
        ];

        if (empty($this->config['api_key']) || empty($this->config['api_secret'])) {
            $this->markTestSkipped('BINGX_API_KEY and BINGX_API_SECRET must be set in environment variables');
        }

        $http = new BaseHttpClient(
            $this->config['api_key'],
            $this->config['api_secret'],
            $this->config['base_uri']
        );

        $this->client = new BingxClient($http);
    }

    protected function skipIfMissingCredentials(): void
    {
        if (empty($this->config['api_key']) || empty($this->config['api_secret'])) {
            $this->markTestSkipped('API credentials not configured');
        }
    }

    protected function assertValidApiResponse(array $response): void
    {
        $this->assertIsArray($response, 'API response should be an array');
        
        // Check for common BingX API response structure
        if (isset($response['code'])) {
            $this->assertIsInt($response['code'], 'Response code should be integer');
            $this->assertArrayHasKey('msg', $response, 'Response should have message field');
        }
    }

    protected function assertSuccessResponse(array $response): void
    {
        $this->assertValidApiResponse($response);
        
        if (isset($response['code'])) {
            $this->assertEquals(0, $response['code'], 'API call should be successful (code = 0)');
        }
    }
}
