<?php

declare(strict_types=1);

namespace App\OAuth\Token;

interface ResourceOwnerInterface
{
    public function getId(): string;

    public function getEmail(): string;

    public function getAvatarUrl(): string;
}
