<?php

namespace App\YoutubeClient\Response;

class VideoInfoResponse
{
    private string $kind;
    private string $etag;
    private VideoItemList $items;
    private PageInfo $pageInfo;

    public function __construct(
        string $kind = '',
        string $etag = '',
        ?VideoItemList $items = null,
        ?PageInfo $pageInfo = null
    ) {
        $this->kind = $kind;
        $this->etag = $etag;
        $this->items = $items === null ? new VideoItemList() : $items;
        $this->pageInfo = $pageInfo === null ? new PageInfo() : $pageInfo;
    }

    public function getKind(): string
    {
        return $this->kind;
    }

    public function getEtag(): string
    {
        return $this->etag;
    }

    public function getItemList(): VideoItemList
    {
        return $this->items;
    }

    public function getPageInfo(): PageInfo
    {
        return $this->pageInfo;
    }
}
