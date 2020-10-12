<?php

declare(strict_types=1);

namespace App\YoutubeClient\Response;

class VideoItemList
{
    /**
     * @var VideoItem[]
     */
    private array $items;

    /**
     * @param VideoItem[] $items
     */
    public function __construct(array $items = [])
    {
        $this->items = $items;
    }

    /**
     * @return VideoItem[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    public function getFirstItem(): ?VideoItem
    {
        if (sizeof($this->items) < 1) {
            return null;
        }

        return $this->items[0];
    }
}
