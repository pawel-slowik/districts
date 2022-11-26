<?php

declare(strict_types=1);

namespace Districts\Infrastructure\Scraper;

use Districts\Domain\Scraper\Exception\FetchingException;
use Districts\Domain\Scraper\HtmlFetcher;
use Fig\Http\Message\StatusCodeInterface as StatusCode;
use GuzzleHttp\ClientInterface;

class GuzzleHtmlFetcher implements HtmlFetcher
{
    public function __construct(
        private ClientInterface $httpClient,
    ) {
    }

    public function fetchHtml(string $url): string
    {
        $response = $this->httpClient->request("GET", $url);
        if ($response->getStatusCode() !== StatusCode::STATUS_OK) {
            throw new FetchingException();
        }
        $contentType = $response->getHeader("content-type")[0];
        if (explode(";", $contentType)[0] !== "text/html") {
            throw new FetchingException();
        }
        return (string) $response->getBody();
    }
}
