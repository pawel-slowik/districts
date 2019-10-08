<?php

declare(strict_types=1);

namespace Scraper;

interface HtmlFetcher
{
    public function fetchHtml(string $url): string;
}
