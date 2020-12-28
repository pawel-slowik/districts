<?php

declare(strict_types=1);

namespace Districts\Infrastructure;

use Districts\Scraper\HtmlFetcher;
use Districts\Scraper\RuntimeException;

use Fig\Http\Message\StatusCodeInterface as StatusCode;
use GuzzleHttp\Client as HttpClient;

class GuzzleHtmlFetcher implements HtmlFetcher
{
    private $httpClient;

    public function __construct()
    {
        $this->httpClient = new HttpClient();
    }

    public function fetchHtml(string $url): string
    {
        $response = $this->httpClient->request("GET", $url);
        if ($response->getStatusCode() !== StatusCode::STATUS_OK) {
            throw new RuntimeException();
        }
        $contentType = $response->getHeader("content-type")[0];
        if (explode(";", $contentType)[0] !== "text/html") {
            throw new RuntimeException();
        }
        return (string) $response->getBody();
    }
}
