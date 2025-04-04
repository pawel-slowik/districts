<?php

declare(strict_types=1);

namespace Districts\Test\Unit\Scraper\Infrastructure;

use Districts\Scraper\Domain\Exception\FetchingException;
use Districts\Scraper\Infrastructure\PsrHtmlFetcher;
use Fig\Http\Message\StatusCodeInterface as StatusCode;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

#[CoversClass(PsrHtmlFetcher::class)]
class PsrHtmlFetcherTest extends TestCase
{
    private PsrHtmlFetcher $guzzleHtmlFetcher;

    private ResponseInterface&Stub $response;

    protected function setUp(): void
    {
        $this->response = $this->createStub(ResponseInterface::class);

        $httpClient = $this->createStub(ClientInterface::class);
        $httpClient
            ->method("sendRequest")
            ->willReturn($this->response);

        $requestFactory = $this->createStub(RequestFactoryInterface::class);

        $this->guzzleHtmlFetcher = new PsrHtmlFetcher($httpClient, $requestFactory);
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
