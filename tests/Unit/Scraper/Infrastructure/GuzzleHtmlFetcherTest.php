<?php

declare(strict_types=1);

namespace Districts\Test\Unit\Scraper\Infrastructure;

use Districts\Scraper\Domain\Exception\FetchingException;
use Districts\Scraper\Infrastructure\GuzzleHtmlFetcher;
use Fig\Http\Message\StatusCodeInterface as StatusCode;
use GuzzleHttp\ClientInterface;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

/**
 * @covers \Districts\Scraper\Infrastructure\GuzzleHtmlFetcher
 */
class GuzzleHtmlFetcherTest extends TestCase
{
    private GuzzleHtmlFetcher $guzzleHtmlFetcher;

    /** @var ResponseInterface&Stub */
    private ResponseInterface $response;

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
            ->willReturnMap([["content-type", ["text/plain"]]]);

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
            ->willReturnMap([["content-type", ["text/html; charset=utf-8"]]]);

        $body = $this->createStub(StreamInterface::class);
        $body
            ->method("__toString")
            ->willReturn("test");

        $this->response
            ->method("getBody")
            ->willReturn($body);

        $result = $this->guzzleHtmlFetcher->fetchHtml("");

        $this->assertSame("test", $result);
    }
}
