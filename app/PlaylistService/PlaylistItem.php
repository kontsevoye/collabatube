<?php

declare(strict_types=1);

namespace App\PlaylistService;

use App\YoutubeClient\Response\VideoItem;
use DateInterval;
use DateTimeImmutable;

class PlaylistItem
{
    /**
     * @var string youtube video id, eg DhKIa4fHkrU
     */
    private string $id;

    private DateTimeImmutable $publishedAt;

    private string $channelId;

    private string $title;

    private string $description;

    private string $channelTitle;

    private string $thumbnailUrl;

    private int $thumbnailWidth;

    private int $thumbnailHeight;

    private DateInterval $duration;

    public function __construct(
        string $id = '',
        ?DateTimeImmutable $publishedAt = null,
        string $channelId = '',
        string $title = '',
        string $description = '',
        string $channelTitle = '',
        string $thumbnailUrl = '',
        int $thumbnailWidth = 0,
        int $thumbnailHeight = 0,
        ?DateInterval $duration = null
    ) {
        $this->id = $id;
        $this->publishedAt = $publishedAt === null ? new DateTimeImmutable() : $publishedAt;
        $this->channelId = $channelId;
        $this->title = $title;
        $this->description = $description;
        $this->channelTitle = $channelTitle;
        $this->thumbnailUrl = $thumbnailUrl;
        $this->thumbnailWidth = $thumbnailWidth;
        $this->thumbnailHeight = $thumbnailHeight;
        $this->duration = $duration === null ? new DateInterval('PT0S') : $duration;
    }

    public static function fromVideoItem(VideoItem $item): self
    {
        return new self(
            $item->getId(),
            $item->getSnippet()->getPublishedAt(),
            $item->getSnippet()->getChannelId(),
            $item->getSnippet()->getTitle(),
            $item->getSnippet()->getDescription(),
            $item->getSnippet()->getChannelTitle(),
            $item->getSnippet()->getThumbnails()->getMaxres()->getUrl(),
            $item->getSnippet()->getThumbnails()->getMaxres()->getWidth(),
            $item->getSnippet()->getThumbnails()->getMaxres()->getHeight(),
            $item->getContentDetails()->getDuration(),
        );
    }

    public function getId(): string
    {
        return $this->id;
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

    public function getThumbnailUrl(): string
    {
        return $this->thumbnailUrl;
    }

    public function getThumbnailWidth(): int
    {
        return $this->thumbnailWidth;
    }

    public function getThumbnailHeight(): int
    {
        return $this->thumbnailHeight;
    }

    public function getDuration(): DateInterval
    {
        return $this->duration;
    }
}
