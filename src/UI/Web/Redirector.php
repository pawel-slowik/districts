<?php

declare(strict_types=1);

namespace Districts\UI\Web;

use Fig\Http\Message\StatusCodeInterface as StatusCode;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface;
use Nyholm\Psr7\Response;
use Slim\Interfaces\RouteParserInterface;

class Redirector
{
    private $routeParser;

    public function __construct(RouteParserInterface $routeParser)
    {
        $this->routeParser = $routeParser;
    }

    public function redirect(
        Request $request,
        string $routeName,
        array $routeData = []
    ): ResponseInterface {
        $url = $this->routeParser->fullUrlFor($request->getUri(), $routeName, $routeData);
        return (new Response())->withHeader("Location", $url)->withStatus(StatusCode::STATUS_FOUND);
    }
}
