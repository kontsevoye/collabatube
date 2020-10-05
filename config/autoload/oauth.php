<?php

declare(strict_types=1);

return [
    'exchange_timeout' => env('OAUTH_EXCHANGE_TIMEOUT', 10),
    'resource_owner_fetch_timeout' => env('OAUTH_RESOURCE_OWNER_FETCH_TIMEOUT', 10),
    'providers' => [
        'github' => [
            'authorize_url' => 'https://github.com/login/oauth/authorize',
            'client_id' => env('OAUTH_GITHUB_CLIENT_ID'),
            'client_secret' => env('OAUTH_GITHUB_CLIENT_SECRET'),
            'scope' => 'user:email',
            'response_type' => null,
            'access_token_url' => 'https://github.com/login/oauth/access_token',
            'grant_type' => null,
        ],
        'google' => [
            'authorize_url' => 'https://accounts.google.com/o/oauth2/v2/auth',
            'client_id' => env('OAUTH_GOOGLE_CLIENT_ID'),
            'client_secret' => env('OAUTH_GOOGLE_CLIENT_SECRET'),
            'scope' => 'email profile',
            'response_type' => 'code',
            'access_token_url' => 'https://oauth2.googleapis.com/token',
            'grant_type' => 'authorization_code',
        ],
    ],
];
