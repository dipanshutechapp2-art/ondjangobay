<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],
	'alibaba' => [
		'api_url' => env('ALIBABA_API_URL'),
		'api_key' => env('ALIBABA_API_KEY'),
	],
	'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URI'),
    ],

    'facebook' => [
        'client_id' => env('FACEBOOK_CLIENT_ID'),
        'client_secret' => env('FACEBOOK_CLIENT_SECRET'),
        'redirect' => env('FACEBOOK_REDIRECT_URI'),
    ],
	'wholesale2b' => [
		'feed_url' => env('WHOLESALE2B_FEED_URL'),
	],
	'cj' => [
		'email' => env('CJ_EMAIL'),
		'api_key' => env('CJ_API_KEY'),
	],
	'recaptcha' => [
        'site_key' => env('RECAPTCHA_SITE_KEY'),
        'secret_key' => env('RECAPTCHA_SECRET_KEY'),
    ],
	'doba' => [
		'app_key' => env('DOBA_APP_KEY'),
	],
	'autods' => [
		'client_id'    => env('AUTODS_CLIENT_ID'),
		'redirect_uri' => env('AUTODS_REDIRECT_URI'),
		'auth_url'     => env('AUTODS_AUTH_URL'),
		'api_url'      => env('AUTODS_API_URL'),
		'order_url'    => env('AUTODS_PLACE_ORDER_URL'),
	],
	'myus' => [
		'base_url' => env('MYUS_BASE_URL'),
		'api_key'  => env('MYUS_API_KEY'),
		'affiliate_id' => env('MYUS_AFFILIATE_ID'),
		'merchant_id' => env('MYUS_MERCHANT_ID'),
		'auth_token'   => env('MYUS_AUTH_TOKEN'),
		'webhook_secret' => env('MYUS_WEBHOOK_SECRET'),
	],
];