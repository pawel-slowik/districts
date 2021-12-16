<?php

declare(strict_types=1);

namespace Districts\Test\UI\Web\View;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Slim\Interfaces\RouteInterface;
use Slim\Interfaces\RouteParserInterface;
use Slim\Routing\RouteContext;
use Slim\Routing\RoutingResults;

trait NamedRequestTester
{
    private function createRequestAttributes(?string $routeName, array $arguments = []): array
    {
        $route = $this->createMock(RouteInterface::class);
        $route->method("getName")->willReturn($routeName);
        $route->method("getArguments")->willReturn($arguments);
        return [
            RouteContext::ROUTE => $route,
            RouteContext::ROUTE_PARSER => $this->createMock(RouteParserInterface::class),
            RouteContext::ROUTING_RESULTS => $this->createMock(RoutingResults::class),
            RouteContext::BASE_PATH => null,
        ];
    }

    private function createRequestMockWithAttributes(array $attributes, array $queryParams = []): ServerRequestInterface
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $request->method("getAttribute")->will(
            $this->returnValueMap(
                $this->convertRequestAttributesToMockArgMap($attributes),
            )
        );
        $request->method("getQueryParams")->willReturn($queryParams);
        $request->method("getUri")->willReturn($this->createMock(UriInterface::class));
        return $request;
    }

    private function convertRequestAttributesToMockArgMap(array $attributes): array
    {
        $map = [];
        foreach ($attributes as $name => $value) {
            $map[] = [$name, null, $value];
        }
        return $map;
    }
}
