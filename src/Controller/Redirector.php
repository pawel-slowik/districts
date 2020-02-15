<?php

declare(strict_types=1);

namespace Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
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
        Response $response,
        string $routeName,
        array $routeData = []
    ): Response {
        $url = $this->routeParser->fullUrlFor($request->getUri(), $routeName, $routeData);
        return $response->withHeader("Location", $url)->withStatus(302);
    }
}
