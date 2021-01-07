<?php

declare(strict_types=1);

namespace Districts\UI\Web\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Interfaces\RouteParserInterface;
use Slim\Views\Twig as View;
use SlimSession\Helper as Session;
use Laminas\Uri\Uri;

use Districts\Application\DistrictService;
use Districts\UI\Web\Factory\ListDistrictsQueryFactory;
use Districts\UI\Web\PageReference;

final class ListController
{
    private $districtService;

    private $queryFactory;

    private $session;

    private $view;

    private $routeParser;

    public function __construct(
        DistrictService $districtService,
        ListDistrictsQueryFactory $queryFactory,
        Session $session,
        View $view,
        RouteParserInterface $routeParser
    ) {
        $this->districtService = $districtService;
        $this->queryFactory = $queryFactory;
        $this->session = $session;
        $this->view = $view;
        $this->routeParser = $routeParser;
    }

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $query = $this->queryFactory->fromRequest($request, $args);
        $districts = $this->districtService->list($query);
        $queryParams = $request->getQueryParams();
        $templateData = [
            "title" => "List of districts",
            "districts" => $districts,
            "orderColumn" => $args["column"] ?? null,
            "orderDirection" => $args["direction"] ?? null,
            "filterColumn" => $queryParams["filterColumn"] ?? null,
            "filterValue" => $queryParams["filterValue"] ?? null,
            "pagination" => iterator_to_array($this->createPagination(
                $this->routeParser->fullUrlFor($request->getUri(), "list", $args, $request->getQueryParams()),
                $districts->getPageCount(),
                is_null($query->getPagination()) ? 1 : $query->getPagination()->getPageNumber(),
            )),
            "successMessage" => $this->session["success.message"],
        ];
        unset($this->session["success.message"]);
        return $this->view->render($response, "list.html", $templateData);
    }

    private static function createPagination(string $baseUrl, int $pageCount, int $currentPageNumber): \Traversable
    {
        if ($pageCount <= 1) {
            return;
        }
        yield new PageReference(
            ($currentPageNumber === 1) ? null : self::urlForPageNumber($baseUrl, $currentPageNumber - 1),
            "previous",
            false,
            true,
            false,
        );
        foreach (range(1, $pageCount) as $pageNumber) {
            yield new PageReference(
                self::urlForPageNumber($baseUrl, $pageNumber),
                strval($pageNumber),
                $pageNumber === $currentPageNumber,
                false,
                false,
            );
        }
        yield new PageReference(
            ($currentPageNumber === $pageCount) ? null : self::urlForPageNumber($baseUrl, $currentPageNumber + 1),
            "next",
            false,
            false,
            true,
        );
    }

    private static function urlForPageNumber(string $baseUrl, int $pageNumber): string
    {
        $parsedUrl = new Uri($baseUrl);
        $parsedUrl->setQuery(array_merge($parsedUrl->getQueryAsArray(), ["page" => $pageNumber]));
        return $parsedUrl->toString();
    }
}
