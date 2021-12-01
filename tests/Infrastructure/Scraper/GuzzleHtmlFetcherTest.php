<?php

declare(strict_types=1);

namespace Districts\Test\Infrastructure\Scraper;

use Districts\DomainModel\Scraper\Exception\FetchingException;
use Districts\Infrastructure\Scraper\GuzzleHtmlFetcher;
use Fig\Http\Message\StatusCodeInterface as StatusCode;
use GuzzleHttp\ClientInterface;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

/**
 * @covers \Districts\Infrastructure\Scraper\GuzzleHtmlFetcher
 */
class GuzzleHtmlFetcherTest extends TestCase
{
    private GuzzleHtmlFetcher $guzzleHtmlFetcher;

    /**
     * @var ResponseInterface
     */
    private $response;

    protected function setUp(): void
    {
        $this->response = $this->createStub(ResponseInterface::class);

        $httpClient = $this->createStub(ClientInterface::class);
        $httpClient
            ->method("request")
            ->willReturn($this->response);

        $this->guzzleHtmlFetcher = new GuzzleHtmlFetcher($httpClient);
    }

    public function testExceptionOnError(): void
    {
        $this->response
            ->method("getStatusCode")
            ->willReturn(StatusCode::STATUS_CREATED);

        $this->expectException(FetchingException::class);

        $this->guzzleHtmlFetcher->fetchHtml("");
    }

    public function testExceptionOnUnexpectedType(): void
    {
        $this->response
            ->method("getStatusCode")
            ->willReturn(StatusCode::STATUS_OK);

        $this->response
            ->method("getHeader")
            ->with($this->identicalTo("content-type"))
            ->willReturn(["text/plain"]);

        $this->expectException(FetchingException::class);

        $this->guzzleHtmlFetcher->fetchHtml("");
    }

    public function testReturnsBody(): void
    {
        $this->response
            ->method("getStatusCode")
            ->willReturn(StatusCode::STATUS_OK);

        $this->response
            ->method("getHeader")
            ->with($this->identicalTo("content-type"))
            ->willReturn(["text/html; charset=utf-8"]);

        $this->response
            ->method("getBody")
            ->willReturn("test");

        $result = $this->guzzleHtmlFetcher->fetchHtml("");

        $this->assertSame("test", $result);
    }
}
