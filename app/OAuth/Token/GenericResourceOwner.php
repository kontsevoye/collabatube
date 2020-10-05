<?php

declare(strict_types=1);

namespace App\OAuth\Token;

class GenericResourceOwner implements ResourceOwnerInterface
{
    private string $id;

    private string $email;

    private string $avatarUrl;

    public function __construct(string $id, string $email, string $avatarUrl)
    {
        $this->id = $id;
        $this->email = $email;
        $this->avatarUrl = $avatarUrl;
    }

    /**
     * {@inheritdoc}
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * {@inheritdoc}
     */
    public function getAvatarUrl(): string
    {
        return $this->avatarUrl;
    }
}
