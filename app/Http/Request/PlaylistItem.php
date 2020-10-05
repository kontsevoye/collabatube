<?php

declare(strict_types=1);

namespace App\Http\Request;

use App\Validator\ValidatorAwareInterface;
use Symfony\Component\Validator\Constraints as Assert;

class PlaylistItem implements ValidatorAwareInterface
{
    /**
     * @var string youtube video url, eg https://www.youtube.com/watch?v=DhKIa4fHkrU
     * @Assert\NotBlank
     */
    private string $url;

    /**
     * @var int video length in seconds
     * @Assert\NotBlank
     */
    private int $length;

    public function __construct(string $url = '', int $length = 0)
    {
        $this->url = $url;
        $this->length = $length;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getLength(): int
    {
        return $this->length;
    }
}
