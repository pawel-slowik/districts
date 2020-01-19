<?php

declare(strict_types=1);

namespace Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Slim\Interfaces\RouteParserInterface;
use Slim\Views\Twig as View;
use SlimSession\Helper as Session;

use Repository\CityRepository;
use Repository\DistrictRepository;

abstract class BaseCrudController
{
    protected $cityRepository;

    protected $districtRepository;

    protected $session;

    protected $routeParser;

    protected $view;

    public function __construct(
        CityRepository $cityRepository,
        DistrictRepository $districtRepository,
        Session $session,
        RouteParserInterface $routeParser,
        View $view
    ) {
        $this->cityRepository = $cityRepository;
        $this->districtRepository = $districtRepository;
        $this->session = $session;
        $this->routeParser = $routeParser;
        $this->view = $view;
    }

    protected function redirectToListResponse(Request $request, Response $response)
    {
        $url = $this->routeParser->fullUrlFor($request->getUri(), "list");
        return $response->withHeader("Location", $url)->withStatus(302);
    }
}
