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
        $this->sourceKey = $sourceKey ?? '';
        // BingX verifies lowercase hexadecimal HMAC-SHA256 signatures. Keep
        // base64 as an explicit legacy option, but never select it implicitly.
        $this->signatureEncoding = strtolower($signatureEncoding) === 'base64'
            ? 'base64'
            : 'hex';
        $this->http = $http ?: new Client([
            'timeout' => 30,
            'connect_timeout' => 10,
        ]);
    }

    protected function timestamp(): string
    {
        return (string)floor(microtime(true) * 1000);
    }

    /**
     * Convert an API parameter to the exact scalar representation used for
     * signing. Arrays and objects are encoded as JSON rather than expanded
     * into PHP-style bracket notation.
     */
    protected function parameterValueToString($value): ?string
    {
        if ($value === null) {
            return null;
        }

        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        if (is_string($value) || is_int($value)) {
            return (string) $value;
        }

        if (is_float($value)) {
            // json_encode uses PHP's round-trip float representation. The
            // typed spot API accepts decimal strings to avoid this path
            // altogether for precision-sensitive order values.
            $json = json_encode($value, JSON_PRESERVE_ZERO_FRACTION);
            if ($json === false) {
                throw new \InvalidArgumentException('Unable to encode floating-point request parameter');
            }

            return $json;
        }

        if (is_array($value) || is_object($value)) {
            $json = json_encode($value, JSON_UNESCAPED_SLASHES);
            if ($json === false) {
                throw new \InvalidArgumentException('Unable to JSON encode request parameter');
            }

            return $json;
        }

        return (string) $value;
    }

    /**
     * Build the raw, sorted canonical string which BingX signs. Do not URL
     * encode this string: URL encoding before HMAC generation causes a
     * signature mismatch for batch orders and other JSON-valued parameters.
     */
    protected function buildCanonicalQuery(array $params): string
    {
        unset($params['signature']);
        ksort($params, SORT_STRING);

        $parts = [];
        foreach ($params as $key => $value) {
            $value = $this->parameterValueToString($value);
            if ($value !== null) {
                $parts[] = $key . '=' . $value;
            }
        }

        return implode('&', $parts);
    }

    /**
     * Build the request query/body and append the signature last. BingX
     * expects the raw canonical form for form bodies; JSON-valued parameters
     * are URL-escaped only when placed in a URL query.
     */
    protected function buildSignedQuery(array $params, string $signature, bool $forUrl): string
    {
        unset($params['signature']);
        ksort($params, SORT_STRING);

        $parts = [];
        foreach ($params as $key => $value) {
            $value = $this->parameterValueToString($value);
            if ($value === null) {
                continue;
            }

            if ($forUrl && (strpos($value, '[') !== false || strpos($value, '{') !== false)) {
                $value = urlencode($value);
            }

            $parts[] = $key . '=' . $value;
        }

        $parts[] = 'signature=' . urlencode($signature);

        return implode('&', $parts);
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
        $headers = [
            'X-BX-APIKEY' => $this->apiKey,
            'Content-Type' => $contentType,
        ];

        if ($this->sourceKey !== '') {
            $headers['X-SOURCE-KEY'] = $this->sourceKey;
        }

        return $headers;
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
        return strpos($message, 'timeout') !== false
            || strpos($message, 'timed out') !== false
            || strpos($message, 'connection') !== false;
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
            case '100412':
                throw new AuthenticationException($message, $response);
            case '100005':
            case '100429':
                throw new RateLimitException($message, $response);
            case '200001':
            case '200002':
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
        
        unset($params['signature']);

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
        $canonicalQuery = $this->buildCanonicalQuery($params);
        $signature = $signed ? $this->signString($canonicalQuery) : null;
        
        $contentType = $bodyType === 'json' 
            ? 'application/json' 
            : 'application/x-www-form-urlencoded';
        
        $headers = $this->headers($contentType);
        $opts = [RequestOptions::HEADERS => $headers];
        
        if ($method === 'GET' || $method === 'DELETE') {
            $opts[RequestOptions::QUERY] = $signed
                ? $this->buildSignedQuery($params, $signature, true)
                : $canonicalQuery;
        } else {
            if ($bodyType === 'json') {
                $bodyParams = $params;
                if ($signature) {
                    $bodyParams['signature'] = $signature;
                }
                $opts[RequestOptions::JSON] = $bodyParams;
            } else {
                $opts[RequestOptions::BODY] = $signed
                    ? $this->buildSignedQuery($params, $signature, false)
                    : $canonicalQuery;
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
            if ($responseData) {
                $this->handleApiError($responseData);
            }
            
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
