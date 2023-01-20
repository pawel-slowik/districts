<?php

declare(strict_types=1);

namespace Districts\Scraper\Domain;

interface HtmlFetcher
{
    public function fetchHtml(string $url): string;
}
