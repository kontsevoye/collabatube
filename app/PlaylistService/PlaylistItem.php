<?php

declare(strict_types=1);

namespace App\PlaylistService;

use App\Http\Request\PlaylistItem as PlaylistItemRequest;

class PlaylistItem
{
    /**
     * @var string youtube video url, eg https://www.youtube.com/watch?v=DhKIa4fHkrU
     */
    private string $url;

    /**
     * @var int video length in seconds
     */
    private int $length;

    public function __construct(string $url, int $length)
    {
        $this->url = $url;
        $this->length = $length;
    }

    public static function fromRequest(PlaylistItemRequest $item): self
    {
        return new self($item->getUrl(), $item->getLength());
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
