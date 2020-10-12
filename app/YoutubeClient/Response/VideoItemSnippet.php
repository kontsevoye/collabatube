<?php

declare(strict_types=1);

namespace App\YoutubeClient\Response;

use DateTimeImmutable;

class VideoItemSnippet
{
    private DateTimeImmutable $publishedAt;

    private string $channelId;

    private string $title;

    private string $description;

    private string $channelTitle;

    private Thumbnails $thumbnails;

    public function __construct(
        ?DateTimeImmutable $publishedAt = null,
        string $channelId = '',
        string $title = '',
        string $description = '',
        string $channelTitle = '',
        ?Thumbnails $thumbnails = null
    ) {
        $this->publishedAt = $publishedAt === null ? new DateTimeImmutable() : $publishedAt;
        $this->channelId = $channelId;
        $this->title = $title;
        $this->description = $description;
        $this->channelTitle = $channelTitle;
        $this->thumbnails = $thumbnails === null ? new Thumbnails() : $thumbnails;
    }

    public function getPublishedAt(): DateTimeImmutable
    {
        return $this->publishedAt;
    }

    public function getChannelId(): string
    {
        return $this->channelId;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getChannelTitle(): string
    {
        return $this->channelTitle;
    }

    public function getThumbnails(): Thumbnails
    {
        return $this->thumbnails;
    }
}
