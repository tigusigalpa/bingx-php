<?php

namespace Tigusigalpa\BingX\Tests\Integration;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use Tigusigalpa\BingX\BingxClient;

/**
 * Opt-in smoke tests for BingX Virtual Simulation Trading.
 *
 * These tests never create an order or alter a VST balance. Enable them only
 * with VST-only API credentials by setting BINGX_RUN_VST_TESTS=true.
 */
class VstDemoTest extends TestCase
{
    private BingxClient $client;
    private string $symbol;

    protected function setUp(): void
    {
        parent::setUp();

        if (!filter_var(getenv('BINGX_RUN_VST_TESTS') ?: false, FILTER_VALIDATE_BOOLEAN)) {
            $this->markTestSkipped('Set BINGX_RUN_VST_TESTS=true to enable VST integration tests.');
        }

        $apiKey = getenv('BINGX_VST_API_KEY') ?: '';
        $apiSecret = getenv('BINGX_VST_API_SECRET') ?: '';

        if ($apiKey === '' || $apiSecret === '') {
            $this->markTestSkipped('BINGX_VST_API_KEY and BINGX_VST_API_SECRET must be set.');
        }

        $this->client = BingxClient::newDemoClient(
            $apiKey,
            $apiSecret,
            getenv('BINGX_SOURCE_KEY') ?: null
        );
        $this->symbol = getenv('BINGX_VST_TEST_SYMBOL') ?: 'BTC-USDT';
    }

    #[Group('vst')]
    public function testDemoClientCanReadMarketData(): void
    {
        $this->assertTrue($this->client->isDemo());
        $this->assertSame(BingxClient::DEMO_BASE_URI, $this->client->getEndpoint());

        $response = $this->client->market()->getLatestPrice($this->symbol);

        $this->assertSame(0, $response['code'] ?? null);
    }

    #[Group('vst')]
    public function testDemoClientCanReadItsAccount(): void
    {
        $response = $this->client->account()->getBalance();

        $this->assertSame(0, $response['code'] ?? null);
    }
}
