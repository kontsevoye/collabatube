<?php

declare(strict_types=1);

namespace App\YoutubeClient\Response;

use DateInterval;

class VideoItemContentDetails
{
    /**
     * @var string eg PT51M19S
     */
    private string $duration;

    public function __construct(string $duration = '')
    {
        $this->duration = $duration;
    }

    public function getRawDuration(): string
    {
        return $this->duration;
    }

    public function getDuration(): DateInterval
    {
        return new DateInterval($this->duration);
    }
}
