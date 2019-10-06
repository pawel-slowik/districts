<?php

declare(strict_types=1);

namespace Controller;

use Psr\Http\Message\ResponseInterface as Response;

use Slim\Router;
use Slim\Views\Twig as View;
use Slim\Http\StatusCode;

use Repository\DistrictRepository;

abstract class BaseCrudController
{
    protected $repository;

    protected $router;

    protected $view;

    public function __construct(DistrictRepository $repository, Router $router, View $view)
    {
        $this->repository = $repository;
        $this->router = $router;
        $this->view = $view;
    }

    protected function redirectToListResponse(Response $response)
    {
        $url = $this->router->pathFor("list");
        return $response->withHeader("Location", $url)->withStatus(StatusCode::HTTP_FOUND);
    }
}
