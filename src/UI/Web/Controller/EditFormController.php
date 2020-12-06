<?php

declare(strict_types=1);

namespace Districts\UI\Web\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpNotFoundException;
use Slim\Views\Twig as View;
use SlimSession\Helper as Session;

use Districts\UI\Web\Factory\GetDistrictQueryFactory;
use Districts\Service\DistrictService;
use Districts\Service\NotFoundException;

final class EditFormController
{
    private $districtService;

    private $queryFactory;

    private $session;

    private $view;

    public function __construct(
        DistrictService $districtService,
        GetDistrictQueryFactory $queryFactory,
        Session $session,
        View $view
    ) {
        $this->districtService = $districtService;
        $this->queryFactory = $queryFactory;
        $this->session = $session;
        $this->view = $view;
    }

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $district = $this->session["form.edit.values"];
        if (!$district) {
            try {
                $district = $this->districtService->get($this->queryFactory->fromRequest($request, $args));
            } catch (NotFoundException $exception) {
                throw new HttpNotFoundException($request);
            }
        }
        $templateData = [
            "title" => "Edit a district",
            "district" => $district,
            "errors" => $this->session["form.edit.errors"],
        ];
        unset($this->session["form.edit.values"]);
        unset($this->session["form.edit.errors"]);
        return $this->view->render($response, "edit.html", $templateData);
    }
}
