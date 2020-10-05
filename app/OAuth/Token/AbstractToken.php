<?php

declare(strict_types=1);

namespace App\OAuth\Token;

abstract class AbstractToken implements AccessTokenInterface
{
    protected string $accessToken;

    /**
     * {@inheritdoc}
     */
    public function getRawAccessToken(): string
    {
        return $this->accessToken;
    }

    /**
     * {@inheritdoc}
     */
    public function loadAccessToken(string $token): AccessTokenInterface
    {
        $this->accessToken = $token;

        return $this;
    }
}
