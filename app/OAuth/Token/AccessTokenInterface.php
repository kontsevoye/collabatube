<?php

declare(strict_types=1);

namespace App\OAuth\Token;

use App\Exception\OAuth\ResourceOwnerFieldMissingException;

interface AccessTokenInterface
{
    public function getRawAccessToken(): string;

    public function loadAccessToken(string $token): AccessTokenInterface;

    /**
     * @throws ResourceOwnerFieldMissingException
     */
    public function getResourceOwner(): ResourceOwnerInterface;
}
