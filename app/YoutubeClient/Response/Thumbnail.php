<?php

declare(strict_types=1);

namespace App\YoutubeClient\Response;

class Thumbnail
{
    private string $url;

    private int $width;

    private int $height;

    public function __construct(string $url = '', int $width = 0, int $height = 0)
    {
        $this->url = $url;
        $this->width = $width;
        $this->height = $height;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getWidth(): int
    {
        return $this->width;
    }

    public function getHeight(): int
    {
        return $this->height;
    }
}
