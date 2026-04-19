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
    protected string $sourceKey;
    protected string $signatureEncoding;
    protected Client $http;
    
    /**
     * Fallback URLs for network/timeout errors
     */
    protected array $fallbackUrls = [
        'https://open-api.bingx.com' => 'https://open-api.bingx.pro',
        'https://open-api-vst.bingx.com' => 'https://open-api-vst.bingx.pro',
    ];
    
    /**
     * Default source key required by BingX API
     */
    protected const DEFAULT_SOURCE_KEY = 'BX-AI-SKILL';

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
        $this->baseUri = rtrim($baseUri, '/');
        $this->sourceKey = $sourceKey ?? self::DEFAULT_SOURCE_KEY;
        $this->signatureEncoding = $signatureEncoding;
        $this->http = $http ?: new Client([
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

    protected function headers(string $contentType = 'application/x-www-form-urlencoded'): array
    {
        return [
            'X-BX-APIKEY' => $this->apiKey,
            'X-SOURCE-KEY' => $this->sourceKey,
            'Content-Type' => $contentType,
        ];
    }
    
    /**
     * Check if exception is a network or timeout error
     */
    protected function isNetworkOrTimeout(\Throwable $e): bool
    {
        if ($e instanceof RequestException) {
            // Connection timeout, DNS errors, etc.
            if ($e->getHandlerContext()['errno'] ?? 0) {
                return true;
            }
            // No response received
            if (!$e->hasResponse()) {
                return true;
            }
        }
        
        // Check for timeout in message
        $message = strtolower($e->getMessage());
        return str_contains($message, 'timeout') 
            || str_contains($message, 'timed out')
            || str_contains($message, 'connection');
    }

    protected function handleApiError(array $response): void
    {
        if (!isset($response['code'])) return;
        
        $code = $response['code'];
        
        // Success codes - no error
        if ($code === 0 || $code === '0') {
            return;
        }
        
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

    /**
     * Make API request with automatic fallback to .pro domain
     * 
     * @param string $method HTTP method (GET, POST, DELETE, PUT)
     * @param string $path API endpoint path
     * @param array $params Request parameters
     * @param bool $signed Whether to sign the request (default: true)
     * @param string $bodyType Body type: 'form' or 'json' (default: 'form')
     * @return array Response data
     * @throws BingxException
     */
    public function request(
        string $method, 
        string $path, 
        array $params = [], 
        bool $signed = true,
        string $bodyType = 'form'
    ): array {
        $method = strtoupper($method);
        
        if ($signed) {
            $params['timestamp'] = $params['timestamp'] ?? $this->timestamp();
        }
        
        $urls = [$this->baseUri];
        if (isset($this->fallbackUrls[$this->baseUri])) {
            $urls[] = $this->fallbackUrls[$this->baseUri];
        }
        
        $lastException = null;
        
        foreach ($urls as $baseUrl) {
            try {
                return $this->executeRequest($baseUrl, $method, $path, $params, $signed, $bodyType);
            } catch (\Throwable $e) {
                $lastException = $e;
                
                // Only fallback on network/timeout errors
                if (!$this->isNetworkOrTimeout($e) || $baseUrl === end($urls)) {
                    throw $e;
                }
            }
        }
        
        throw $lastException ?? new BingxException('Request failed with no response');
    }
    
    /**
     * Execute the actual HTTP request
     */
    protected function executeRequest(
        string $baseUrl,
        string $method, 
        string $path, 
        array $params, 
        bool $signed,
        string $bodyType
    ): array {
        $query = $this->buildQuery($params);
        $signature = $signed ? $this->signString($query) : null;
        
        $contentType = $bodyType === 'json' 
            ? 'application/json' 
            : 'application/x-www-form-urlencoded';
        
        $headers = $this->headers($contentType);
        $opts = [RequestOptions::HEADERS => $headers];
        
        if ($method === 'GET' || $method === 'DELETE') {
            $queryParams = $params;
            if ($signature) {
                $queryParams['signature'] = $signature;
            }
            $opts[RequestOptions::QUERY] = $queryParams;
        } else {
            if ($bodyType === 'json') {
                $bodyParams = $params;
                if ($signature) {
                    $bodyParams['signature'] = $signature;
                }
                $opts[RequestOptions::JSON] = $bodyParams;
            } else {
                $bodyParams = $params;
                if ($signature) {
                    $bodyParams['signature'] = $signature;
                }
                $opts[RequestOptions::FORM_PARAMS] = $bodyParams;
            }
        }

        try {
            $url = $baseUrl . $path;
            $response = $this->http->request($method, $url, $opts);
            $body = (string)$response->getBody();
            
            // Use JSON_BIGINT_AS_STRING to handle large integers (orderId, etc.)
            $data = json_decode($body, true, 512, JSON_BIGINT_AS_STRING);
            
            if (!is_array($data)) {
                throw new BingxException('Invalid JSON response from API', 0, null, ['raw' => $body]);
            }
            
            // Handle API-level errors
            $this->handleApiError($data);
            
            return $data;
            
        } catch (RequestException $e) {
            $responseBody = $e->hasResponse() ? (string)$e->getResponse()->getBody() : '';
            $responseData = json_decode($responseBody, true, 512, JSON_BIGINT_AS_STRING) ?: [];
            
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
