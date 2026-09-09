<?php

declare(strict_types=1);

use Tigusigalpa\BingX\BingxClient;

require __DIR__ . '/../vendor/autoload.php';

$apiKey = getenv('BINGX_VST_API_KEY') ?: '';
$apiSecret = getenv('BINGX_VST_API_SECRET') ?: '';
$sourceKey = getenv('BINGX_SOURCE_KEY') ?: null;
$symbol = getenv('BINGX_VST_TEST_SYMBOL') ?: 'BTC-USDT';

if ($apiKey === '' || $apiSecret === '') {
    throw new RuntimeException(
        'Set BINGX_VST_API_KEY and BINGX_VST_API_SECRET to API keys created for BingX VST.'
    );
}

$demo = BingxClient::newDemoClient($apiKey, $apiSecret, $sourceKey);

if (!$demo->isDemo()) {
    throw new LogicException('Refusing to continue: this example must use the VST endpoint.');
}

echo 'Environment: ' . $demo->getEnvironment() . PHP_EOL;
echo 'Endpoint: ' . $demo->getEndpoint() . PHP_EOL;

// Safe read-only calls: VST uses live market prices but virtual account funds.
$price = $demo->market()->getLatestPrice($symbol);
$balance = $demo->account()->getBalance();

echo 'Last price response:' . PHP_EOL;
echo json_encode($price, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL;
echo 'Account balance response:' . PHP_EOL;
echo json_encode($balance, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL;

// Safe order validation: BingX validates the payload but does not create an order.
$testOrder = $demo->trade()->createTestOrder([
    'symbol' => $symbol,
    'side' => 'BUY',
    'positionSide' => 'LONG',
    'type' => 'MARKET',
    'quantity' => '0.001',
]);

echo 'Test order response:' . PHP_EOL;
echo json_encode($testOrder, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL;

// This endpoint can alter virtual VST funds. Opt in deliberately; it never
// affects real funds. "0" requests an increase and "1" a decrease.
if (filter_var(getenv('BINGX_REQUEST_VST') ?: false, FILTER_VALIDATE_BOOLEAN)) {
    $vst = $demo->trade()->adjustVst(0);
    echo 'VST response:' . PHP_EOL;
    echo json_encode($vst, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL;
}

// A real VST order is also deliberately opt-in. It is simulated but will
// create a position/order in the VST account.
if (filter_var(getenv('BINGX_PLACE_VST_ORDER') ?: false, FILTER_VALIDATE_BOOLEAN)) {
    $order = $demo->trade()->createOrder([
        'symbol' => $symbol,
        'side' => 'BUY',
        'positionSide' => 'LONG',
        'type' => 'MARKET',
        'quantity' => '0.001',
    ]);

    echo 'VST order response:' . PHP_EOL;
    echo json_encode($order, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL;
}
