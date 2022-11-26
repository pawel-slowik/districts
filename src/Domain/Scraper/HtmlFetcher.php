<?php

declare(strict_types=1);

namespace Districts\Domain\Scraper;

interface HtmlFetcher
{
    public function fetchHtml(string $url): string;
}
