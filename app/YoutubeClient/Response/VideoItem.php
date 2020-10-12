<?php

declare(strict_types=1);

namespace App\YoutubeClient\Response;

class VideoItem
{
    private string $kind;

    private string $etag;

    private string $id;

    private VideoItemSnippet $snippet;

    private VideoItemContentDetails $contentDetails;

    public function __construct(
        string $kind = '',
        string $etag = '',
        string $id = '',
        ?VideoItemSnippet $snippet = null,
        ?VideoItemContentDetails $contentDetails = null
    ) {
        $this->kind = $kind;
        $this->etag = $etag;
        $this->id = $id;
        $this->snippet = $snippet === null ? new VideoItemSnippet() : $snippet;
        $this->contentDetails = $contentDetails === null ? new VideoItemContentDetails() : $contentDetails;
    }

    public function getKind(): string
    {
        return $this->kind;
    }

    public function getEtag(): string
    {
        return $this->etag;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getSnippet(): VideoItemSnippet
    {
        return $this->snippet;
    }

    public function getContentDetails(): VideoItemContentDetails
    {
        return $this->contentDetails;
    }
}
