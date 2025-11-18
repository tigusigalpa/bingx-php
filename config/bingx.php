<?php
return [
    'api_key' => env('BINGX_API_KEY', ''),
    'api_secret' => env('BINGX_API_SECRET', ''),
    'source_key' => env('BINGX_SOURCE_KEY', null),
    'base_uri' => env('BINGX_BASE_URI', 'https://open-api.bingx.com'),
    'signature_encoding' => env('BINGX_SIGNATURE_ENCODING', 'base64')
];