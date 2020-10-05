<?php

declare(strict_types=1);

namespace App\OAuth\Provider;

use App\OAuth\Token\AccessTokenInterface;
use App\OAuth\Token\GoogleToken;

class GoogleProvider extends AbstractProvider
{
    /**
     * {@inheritdoc}
     */
    public function getProviderName(): string
    {
        return 'google';
    }

    /**
     * {@inheritdoc}
     */
    public function getAccessTokenFieldName(): string
    {
        return 'id_token';
    }

    /**
     * {@inheritdoc}
     */
    protected function getAccessTokenInstance(): AccessTokenInterface
    {
        return new GoogleToken();
    }
}
