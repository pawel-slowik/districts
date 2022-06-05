<?php

declare(strict_types=1);

namespace Districts\UI\Web\Controller;

use Districts\Application\DistrictService;
use Districts\Application\Exception\NotFoundException;
use Districts\DomainModel\Exception\DistrictNotFoundException;
use Districts\UI\Web\Factory\GetDistrictQueryFactory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpNotFoundException;
use Slim\Views\Twig as View;
use SlimSession\Helper as Session;

final class EditFormController
{
    public function __construct(
        private DistrictService $districtService,
        private GetDistrictQueryFactory $queryFactory,
        private Session $session,
        private View $view,
    ) {
    }

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $district = $this->session->get("form.edit.values");
        if (!$district) {
            try {
                $district = $this->districtService->get($this->queryFactory->fromRequest($request, $args));
            } catch (DistrictNotFoundException | NotFoundException $exception) {
                throw new HttpNotFoundException($request);
            }
        }
        $templateData = [
            "title" => "Edit a district",
            "district" => $district,
            "errors" => $this->session->get("form.edit.errors"),
            "errorMessage" => $this->session->get("form.edit.error.message"),
        ];
        $this->session->delete("form.edit.error.message");
        $this->session->delete("form.edit.values");
        $this->session->delete("form.edit.errors");
        return $this->view->render($response, "edit.html", $templateData);
    }
}
