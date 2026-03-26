<?php
return [
    'env' => env('DHL_ENV', 'sandbox'),
    'sandbox_base_url' => env('DHL_SANDBOX_BASE_URL'),
    'prod_base_url' => env('DHL_PROD_BASE_URL'),
    'username' => env('DHL_USERNAME'),
    'password' => env('DHL_PASSWORD'),
	'account_number' => env('DHL_ACCOUNT_NUMBER'),
];