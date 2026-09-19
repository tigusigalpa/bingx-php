<?php

namespace Tigusigalpa\BingX\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Tigusigalpa\BingX\Exceptions\ApiException;
use Tigusigalpa\BingX\Exceptions\AuthenticationException;
use Tigusigalpa\BingX\Exceptions\BingxException;
use Tigusigalpa\BingX\Exceptions\InsufficientBalanceException;
use Tigusigalpa\BingX\Exceptions\RateLimitException;
use Tigusigalpa\BingX\Facades\Bingx;

class ExceptionAndFacadeTest extends TestCase
{
    public function testExceptionsPreserveApiMetadataAndTheExceptionChain(): void
    {
        $previous = new \RuntimeException('root cause');
        $exception = new BingxException('request failed', 42, $previous, ['code' => 42]);

        $this->assertSame(['code' => 42], $exception->getResponse());
        $this->assertSame($previous, $exception->getPrevious());

        $api = new ApiException('api failed', 'API_ERROR', ['code' => 'API_ERROR']);
        $this->assertSame('API_ERROR', $api->getErrorCode());
        $this->assertSame(['code' => 'API_ERROR'], $api->getResponse());

        $this->assertSame('AUTH_ERROR', (new AuthenticationException())->getErrorCode());
        $this->assertSame('INSUFFICIENT_BALANCE', (new InsufficientBalanceException())->getErrorCode());
        $this->assertSame('RATE_LIMIT', (new RateLimitException())->getErrorCode());
    }

    public function testFacadeUsesTheBingxContainerBinding(): void
    {
        $method = new \ReflectionMethod(Bingx::class, 'getFacadeAccessor');
        $method->setAccessible(true);

        $this->assertSame('bingx', $method->invoke(null));
    }
}
