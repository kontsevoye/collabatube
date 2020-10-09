<?php

declare(strict_types=1);

namespace App\Http\Request;

use App\Validator\ValidatorAwareInterface;
use Hyperf\HttpMessage\Uri\Uri;
use Symfony\Component\Validator\Constraints as Assert;

class PlaylistItem implements ValidatorAwareInterface
{
    /**
     * @var string youtube video url, eg https://www.youtube.com/watch?v=DhKIa4fHkrU
     * @Assert\NotBlank
     */
    private string $url;

    public function __construct(string $url = '')
    {
        $this->url = $url;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getUri(): Uri
    {
        return new Uri($this->url);
    }
}
