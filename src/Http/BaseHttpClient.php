<?php

namespace Tigusigalpa\BingX\Http;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\RequestOptions;
use Tigusigalpa\BingX\Exceptions\ApiException;
use Tigusigalpa\BingX\Exceptions\AuthenticationException;
use Tigusigalpa\BingX\Exceptions\BingxException;
use Tigusigalpa\BingX\Exceptions\RateLimitException;
use Tigusigalpa\BingX\Exceptions\InsufficientBalanceException;

class BaseHttpClient
{
    protected string $apiKey;
    protected string $apiSecret;
    protected string $baseUri;
    protected ?string $sourceKey;
    protected string $signatureEncoding;
    protected Client $http;

    public function __construct(
        string $apiKey, 
        string $apiSecret, 
        string $baseUri = 'https://open-api.bingx.com', 
        ?string $sourceKey = null, 
        string $signatureEncoding = 'hex', 
        ?Client $http = null
    ) {
        $this->apiKey = $apiKey;
        $this->apiSecret = $apiSecret;
        $this->baseUri = $baseUri;
        $this->sourceKey = $sourceKey;
        $this->signatureEncoding = $signatureEncoding;
        $this->http = $http ?: new Client([
            'base_uri' => $this->baseUri,
            'timeout' => 30,
            'connect_timeout' => 10,
        ]);
    }

    protected function timestamp(): string
    {
        return (string)floor(microtime(true) * 1000);
    }

    protected function buildQuery(array $params): string
    {
        if (!$params) return '';
        ksort($params);
        return http_build_query($params, '', '&', PHP_QUERY_RFC3986);
    }

    protected function signString(string $string): string
    {
        if ($this->signatureEncoding === 'hex') {
            return hash_hmac('sha256', $string, $this->apiSecret, false);
        }
        $raw = hash_hmac('sha256', $string, $this->apiSecret, true);
        return base64_encode($raw);
    }

    protected function headers(): array
    {
        $headers = [
            'X-BX-APIKEY' => $this->apiKey,
            'Content-Type' => 'application/x-www-form-urlencoded',
        ];
        if ($this->sourceKey) $headers['X-SOURCE-KEY'] = $this->sourceKey;
        return $headers;
    }

    protected function handleApiError(array $response): void
    {
        if (!isset($response['code'])) return;
        
        $code = $response['code'];
        $message = $response['msg'] ?? 'Unknown API error';
        
        // Map common BingX error codes to exceptions
        switch ($code) {
            case '100001':
            case '100002':
            case '100003':
            case '100004':
                throw new AuthenticationException($message, $response);
            case '100005':
                throw new RateLimitException($message, $response);
            case '200001':
                throw new InsufficientBalanceException($message, $response);
            default:
                throw new ApiException($message, $code, $response);
        }
    }

    public function request(string $method, string $path, array $params = [], array $options = []): array
    {
        $method = strtoupper($method);
        $params['timestamp'] = $params['timestamp'] ?? $this->timestamp();
        $query = $this->buildQuery($params);
        $signature = $this->signString($query);
        $headers = $this->headers();
        
        $opts = [RequestOptions::HEADERS => $headers];
        
        if ($method === 'GET') {
            $opts[RequestOptions::QUERY] = $params + ['signature' => $signature];
        } else {
            $opts[RequestOptions::FORM_PARAMS] = $params + ['signature' => $signature];
        }
        
        if ($options) $opts = array_replace_recursive($opts, $options);

        try {
            $response = $this->http->request($method, $path, $opts);
            $body = (string)$response->getBody();
            $data = json_decode($body, true);
            
            if (!is_array($data)) {
                throw new BingxException('Invalid JSON response from API', 0, null, ['raw' => $body]);
            }
            
            // Handle API-level errors
            $this->handleApiError($data);
            
            return $data;
            
        } catch (RequestException $e) {
            $responseBody = $e->hasResponse() ? (string)$e->getResponse()->getBody() : '';
            $responseData = json_decode($responseBody, true) ?: [];
            
            throw new BingxException(
                "HTTP request failed: " . $e->getMessage(),
                $e->getCode(),
                $e,
                $responseData
            );
        }
    }

    public function getEndpoint(): string
    {
        return $this->baseUri;
    }

    public function getApiKey(): string
    {
        return $this->apiKey;
    }
}
