<?php

declare(strict_types=1);

namespace App\Http\Request;

use App\Validator\ValidatorAwareInterface;
use Symfony\Component\Validator\Constraints as Assert;

class Room implements ValidatorAwareInterface
{
    /**
     * @Assert\NotBlank
     */
    private string $name;

    /**
     * @Assert\NotBlank
     */
    private string $avatarUrl;

    public function __construct(string $name = '', string $avatarUrl = '')
    {
        $this->name = $name;
        $this->avatarUrl = $avatarUrl;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getAvatarUrl(): string
    {
        return $this->avatarUrl;
    }
}
