<?php

declare(strict_types=1);

namespace Districts\Scraper\Infrastructure;

use Districts\Scraper\Domain\Exception\FetchingException;
use Districts\Scraper\Domain\HtmlFetcher;
use Fig\Http\Message\StatusCodeInterface as StatusCode;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;

final readonly class PsrHtmlFetcher implements HtmlFetcher
{
    public function __construct(
        private ClientInterface $httpClient,
        private RequestFactoryInterface $requestFactory,
    ) {
    }

    public function fetchHtml(string $url): string
    {
        $request = $this->requestFactory->createRequest("GET", $url);
        $response = $this->httpClient->sendRequest($request);
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
