<?php

declare(strict_types=1);

namespace Districts\Editor\UI\Controller;

use Districts\Editor\Application\DistrictService;
use Districts\Editor\Infrastructure\NotFoundInRepositoryException;
use Districts\Editor\UI\Factory\GetDistrictQueryFactory;
use Districts\Editor\UI\Session;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpNotFoundException;
use Slim\Views\Twig as View;

final class EditFormController
{
    public function __construct(
        private DistrictService $districtService,
        private GetDistrictQueryFactory $queryFactory,
        private Session $session,
        private View $view,
    ) {
    }

    /**
     * @param array<string, string> $args
     */
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $district = $this->session->getAndDelete("form.edit.values");
        if (!$district) {
            try {
                $district = $this->districtService->get($this->queryFactory->fromRequest($request, $args));
            } catch (NotFoundInRepositoryException) {
                throw new HttpNotFoundException($request);
            }
        }
        $templateData = [
            "title" => "Edit a district",
            "district" => $district,
            "errors" => $this->session->getAndDelete("form.edit.errors"),
            "errorMessage" => $this->session->getAndDelete("form.edit.error.message"),
        ];
        return $this->view->render($response, "edit.html", $templateData);
    }
}
