<?php

namespace App\YoutubeClient\Response;

class Thumbnails
{
    private Thumbnail $default;
    private Thumbnail $medium;
    private Thumbnail $high;
    private Thumbnail $standard;
    private Thumbnail $maxres;

    public function __construct(
        ?Thumbnail $default = null,
        ?Thumbnail $medium = null,
        ?Thumbnail $high = null,
        ?Thumbnail $standard = null,
        ?Thumbnail $maxres = null
    ) {
        $this->default = $default === null ? new Thumbnail() : $default;
        $this->medium = $medium === null ? new Thumbnail() : $medium;
        $this->high = $high === null ? new Thumbnail() : $high;
        $this->standard = $standard === null ? new Thumbnail() : $standard;
        $this->maxres = $maxres === null ? new Thumbnail() : $maxres;
    }

    public function getDefault(): Thumbnail
    {
        return $this->default;
    }

    public function getMedium(): Thumbnail
    {
        return $this->medium;
    }

    public function getHigh(): Thumbnail
    {
        return $this->high;
    }

    public function getStandard(): Thumbnail
    {
        return $this->standard;
    }

    public function getMaxres(): Thumbnail
    {
        return $this->maxres;
    }
}
