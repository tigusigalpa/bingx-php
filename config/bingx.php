<?php
return [
    'api_key' => env('BINGX_API_KEY', ''),
    'api_secret' => env('BINGX_API_SECRET', ''),
    'source_key' => env('BINGX_SOURCE_KEY', null),
    // Uses the VST endpoint and virtual funds. Set BINGX_DEMO=true to enable.
    'demo' => env('BINGX_DEMO', false),
    'base_uri' => env('BINGX_BASE_URI', 'https://open-api.bingx.com'),
    // BingX expects lowercase hexadecimal HMAC-SHA256 signatures. Set this to
    // "base64" only when maintaining a legacy integration that explicitly
    // requires it.
    'signature_encoding' => env('BINGX_SIGNATURE_ENCODING', 'hex')
];
