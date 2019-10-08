<?php

declare(strict_types=1);

namespace Scraper;

use GuzzleHttp\Client as HttpClient;

class GuzzleHtmlFetcher implements HtmlFetcher
{
    protected $httpClient;

    public function __construct()
    {
        $this->httpClient = new HttpClient();
    }

    public function fetchHtml(string $url): string
    {
        $response = $this->httpClient->request("GET", $url);
        if ($response->getStatusCode() !== 200) {
            throw new RuntimeException();
        }
        $contentType = $response->getHeader("content-type")[0];
        if (explode(";", $contentType)[0] !== "text/html") {
            throw new RuntimeException();
        }
        return (string) $response->getBody();
    }
}
