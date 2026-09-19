<?php

namespace Tigusigalpa\BingX\Tests\Unit;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionParameter;
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
use Tigusigalpa\BingX\Services\SpotOrderRequest;
use Tigusigalpa\BingX\Services\SpotTradeService;
use Tigusigalpa\BingX\Services\SubAccountService;
use Tigusigalpa\BingX\Services\TradeService;
use Tigusigalpa\BingX\Services\TwapService;
use Tigusigalpa\BingX\Services\WalletService;
use Tigusigalpa\BingX\Tests\Support\RecordingHttpClient;

/**
 * Contract coverage for the SDK's endpoint wrappers.
 *
 * Services in this package are intentionally small adapters around a signed
 * HTTP request. Running every public wrapper through a recording transport
 * catches signature/argument regressions without ever calling BingX.
 */
class ServiceContractCoverageTest extends TestCase
{
    /** @var class-string[] */
    private const SERVICE_CLASSES = [
        AccountService::class,
        CoinMListenKeyService::class,
        CoinMMarketService::class,
        CoinMTradeService::class,
        ContractService::class,
        CopyTradingService::class,
        ListenKeyService::class,
        MarketService::class,
        SpotAccountService::class,
        SpotTradeService::class,
        SubAccountService::class,
        TradeService::class,
        TwapService::class,
        WalletService::class,
    ];

    /**
     * Methods which calculate a local value or start a fluent builder instead
     * of making one API request. They have focused tests elsewhere.
     *
     * @var array<class-string, string[]>
     */
    private const NON_ENDPOINT_METHODS = [
        SpotAccountService::class => ['getFundBalance'],
        TradeService::class => [
            'calculateFuturesCommission',
            'calculateBatchCommission',
            'getCommissionRates',
            'getCommissionAmount',
            'order',
        ],
    ];

    /**
     * @return iterable<string, array{class-string, string}>
     */
    public static function endpointMethods(): iterable
    {
        foreach (self::SERVICE_CLASSES as $class) {
            $reflection = new \ReflectionClass($class);

            foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
                if (
                    $method->isConstructor()
                    || $method->getDeclaringClass()->getName() !== $class
                    || in_array($method->getName(), self::NON_ENDPOINT_METHODS[$class] ?? [], true)
                ) {
                    continue;
                }

                yield $class . '::' . $method->getName() => [$class, $method->getName()];
            }
        }
    }

    /**
     * @param class-string $class
     */
    #[DataProvider('endpointMethods')]
    public function testEveryEndpointWrapperBuildsAnApiRequest(string $class, string $methodName): void
    {
        $http = new RecordingHttpClient();
        $service = new $class($http);
        $method = new ReflectionMethod($service, $methodName);

        $result = $method->invokeArgs($service, $this->argumentsFor($method));

        $this->assertIsArray($result, $class . '::' . $methodName . ' must return an API response array.');
        $this->assertNotEmpty($http->requests, $class . '::' . $methodName . ' must dispatch an HTTP request.');

        $request = $http->requests[array_key_last($http->requests)];
        $this->assertContains($request['method'], ['GET', 'POST', 'PUT', 'DELETE']);
        $this->assertMatchesRegularExpression('#^/openApi/#', $request['path']);
        $this->assertIsArray($request['params']);
    }

    /**
     * Build valid, explicit values so nullable parameters and optional request
     * branches are exercised rather than silently taking their defaults.
     *
     * @return array<int, mixed>
     */
    private function argumentsFor(ReflectionMethod $method): array
    {
        $arguments = [];

        foreach ($method->getParameters() as $parameter) {
            $arguments[] = $this->valueFor($parameter);
        }

        return $arguments;
    }

    /** @return mixed */
    private function valueFor(ReflectionParameter $parameter)
    {
        $type = $parameter->getType();
        $typeName = $type instanceof ReflectionNamedType ? $type->getName() : null;
        $name = $parameter->getName();

        if ($typeName === SpotOrderRequest::class) {
            return new SpotOrderRequest('BTC-USDT', 'BUY', 'LIMIT', '0.001', '50000.00');
        }

        if ($typeName === 'array') {
            if ($name === 'orders') {
                return [[
                    'symbol' => 'BTC-USDT',
                    'side' => 'BUY',
                    'positionSide' => 'LONG',
                    'type' => 'MARKET',
                    'quantity' => '0.001',
                ]];
            }

            if (in_array($name, ['orderIds', 'clientOrderIds'], true)) {
                return ['1000001'];
            }

            if (in_array($name, ['assets', 'coins'], true)) {
                return ['USDT'];
            }

            return [
                'symbol' => 'BTC-USDT',
                'side' => 'BUY',
                'positionSide' => 'LONG',
                'type' => 'MARKET',
                'quantity' => '0.001',
            ];
        }

        if ($typeName === 'bool') {
            return true;
        }

        if ($typeName === 'float') {
            return 1.5;
        }

        if ($typeName === 'int') {
            return $this->integerValueFor($name);
        }

        if ($typeName === 'string') {
            return $this->stringValueFor($name);
        }

        throw new \LogicException('Unsupported endpoint parameter type for ' . $name);
    }

    private function integerValueFor(string $name): int
    {
        if (in_array($name, ['timestamp', 'startTime', 'endTime'], true)) {
            return 1700000000000;
        }

        if ($name === 'recvWindow') {
            return 5000;
        }

        if (in_array($name, ['limit', 'pageSize', 'size', 'current'], true)) {
            return 10;
        }

        if ($name === 'adjustType') {
            return 0;
        }

        if ($name === 'leverage') {
            return 10;
        }

        return 1;
    }

    private function stringValueFor(string $name): string
    {
        $values = [
            'symbol' => 'BTC-USDT',
            'side' => 'BUY',
            'positionSide' => 'LONG',
            'type' => 'MARKET',
            'orderType' => 'MARKET',
            'marginMode' => 'CROSSED',
            'marginType' => 'CROSSED',
            'positionMode' => 'HEDGE',
            'accountType' => 'SpotFund',
            'asset' => 'USDT',
            'coin' => 'USDT',
            'network' => 'ERC20',
            'interval' => '1h',
            'contractType' => 'PERPETUAL',
            'incomeType' => 'ALL',
            'workingType' => 'MARK_PRICE',
            'timeInForce' => 'GTC',
            'cancelReplaceMode' => 'STOP_ON_FAILURE',
            'orderId' => '1000001',
            'cancelOrderId' => '1000001',
            'clientOrderId' => 'client-order-1',
            'cancelClientOrderId' => 'client-order-1',
            'listenKey' => 'listen-key',
            'positionId' => '1000001',
            'subUid' => '1000001',
            'subAccountString' => 'sub-account',
            'transferType' => 'FUND_SFUTURES',
            'transferDirection' => 'IN',
        ];

        return $values[$name] ?? 'value';
    }
}
