<?php

declare(strict_types=1);

namespace Districts\Infrastructure;

use Districts\DomainModel\Scraper\HtmlFetcher;
use Districts\DomainModel\Scraper\RuntimeException;
use Fig\Http\Message\StatusCodeInterface as StatusCode;
use GuzzleHttp\ClientInterface;

class GuzzleHtmlFetcher implements HtmlFetcher
{
    private $httpClient;

    public function __construct(ClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
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
