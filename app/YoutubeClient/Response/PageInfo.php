<?php

declare(strict_types=1);

namespace App\YoutubeClient\Response;

class PageInfo
{
    private int $totalResults;

    private int $resultsPerPage;

    public function __construct(int $totalResults = 0, int $resultsPerPage = 0)
    {
        $this->totalResults = $totalResults;
        $this->resultsPerPage = $resultsPerPage;
    }

    public function getTotalResults(): int
    {
        return $this->totalResults;
    }

    public function getResultsPerPage(): int
    {
        return $this->resultsPerPage;
    }
}
