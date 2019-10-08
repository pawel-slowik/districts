<?php

declare(strict_types=1);

namespace Scraper;

use GuzzleHttp\Client as HttpClient;

interface HtmlFetcher
{
    public function fetchHtml(string $url): string;
}
