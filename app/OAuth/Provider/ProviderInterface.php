<?php

declare(strict_types=1);

namespace App\OAuth\Provider;

use App\Exception\OAuth\ExchangeCodeExceptionInterface;
use App\OAuth\Token\AccessTokenInterface;

interface ProviderInterface
{
    /**
     * @param string $serverHost host of the server, that received client request, eg myapp.com
     * @param string $lastLocation last user location for returning him back after auth flow finish
     * @return string url for passing to client
     */
    public function buildInitializeUrl(string $serverHost, string $lastLocation = '/'): string;

    /**
     * @param string $serverHost host of the server, that received client request, eg myapp.com
     * @param string $code code received from OAuth provider
     * @param string $lastLocation last user location for returning him back after auth flow finish
     * @throws ExchangeCodeExceptionInterface
     * @return AccessTokenInterface access token
     */
    public function exchangeCode(string $serverHost, string $code, string $lastLocation = '/'): AccessTokenInterface;
}
