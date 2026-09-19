<?php

namespace Tigusigalpa\BingX\Tests\Support;

use Tigusigalpa\BingX\Http\BaseHttpClient;

/** @internal Recording test double; never sends a network request. */
class RecordingHttpClient extends BaseHttpClient
{
    /** @var array<int, array{method: string, path: string, params: array}> */
    public array $requests = [];

    public function __construct()
    {
    }

    public function request(
        string $method,
        string $path,
        array $params = [],
        bool $signed = true,
        string $bodyType = 'form'
    ): array {
        $this->requests[] = [
            'method' => $method,
            'path' => $path,
            'params' => $params,
        ];

        return ['code' => 0, 'data' => []];
    }
}
