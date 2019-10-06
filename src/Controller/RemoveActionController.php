<?php

declare(strict_types=1);

namespace Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Slim\Views\Twig as View;
use Slim\Router;
use Slim\Exception\NotFoundException;
use Slim\Http\StatusCode;

use Repository\DistrictRepository;

class RemoveActionController extends BaseCrudController
{
    protected $router;

    public function __construct(DistrictRepository $repository, Router $router, View $view)
    {
        parent::__construct($repository, $view);
        $this->router = $router;
    }

    public function __invoke(Request $request, Response $response, array $args)
    {
        $district = $this->repository->get(intval($args["id"]));
        if (!$district) {
            throw new NotFoundException($request, $response);
        }
        if ($this->isConfirmed($request)) {
            $this->repository->remove($district);
            // TODO: flash message
        }
        return $this->redirectToListResponse($response);
    }

    protected function isConfirmed(Request $request): bool
    {
        $parsed = $request->getParsedBody();
        return array_key_exists("confirm", $parsed);
    }

    protected function redirectToListResponse(Response $response)
    {
        $url = $this->router->pathFor("list");
        return $response->withHeader("Location", $url)->withStatus(StatusCode::HTTP_FOUND);
    }
}
