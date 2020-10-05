<?php

declare(strict_types=1);

namespace App\OAuth\Provider;

use App\OAuth\Token\AccessTokenInterface;
use App\OAuth\Token\GithubToken;

class GithubProvider extends AbstractProvider
{
    /**
     * {@inheritdoc}
     */
    public function getProviderName(): string
    {
        return 'github';
    }

    /**
     * {@inheritdoc}
     */
    protected function getAccessTokenInstance(): AccessTokenInterface
    {
        return new GithubToken($this->config, $this->clientFactory);
    }
}
