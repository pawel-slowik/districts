<?php

declare(strict_types=1);

namespace Districts\Editor\UI\Controller;

use Districts\Core\Infrastructure\NotFoundInRepositoryException;
use Districts\Editor\Application\DistrictService;
use Districts\Editor\UI\Factory\GetDistrictQueryFactory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpNotFoundException;
use Slim\Views\Twig as View;

final class RemoveFormController
{
    public function __construct(
        private DistrictService $districtService,
        private GetDistrictQueryFactory $queryFactory,
        private View $view,
    ) {
    }

    /**
     * @param array<string, string> $args
     */
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        try {
            $district = $this->districtService->get($this->queryFactory->fromRequest($request, $args));
        } catch (NotFoundInRepositoryException) {
            throw new HttpNotFoundException($request);
        }
        $templateData = [
            "title" => "Remove a district",
            "district" => $district,
        ];
        return $this->view->render($response, "remove.html", $templateData);
    }
}
