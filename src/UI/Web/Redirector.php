<?php

declare(strict_types=1);

namespace Districts\UI\Web;

use Fig\Http\Message\StatusCodeInterface as StatusCode;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use Slim\Interfaces\RouteParserInterface;

class Redirector
{
    private $routeParser;

    public function __construct(RouteParserInterface $routeParser)
    {
        $this->routeParser = $routeParser;
    }

    public function redirect(
        UriInterface $baseUri,
        string $routeName,
        array $routeData = []
    ): ResponseInterface {
        $url = $this->routeParser->fullUrlFor($baseUri, $routeName, $routeData);
        return (new Response())->withHeader("Location", $url)->withStatus(StatusCode::STATUS_FOUND);
    }
}
