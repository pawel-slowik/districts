<?php

declare(strict_types=1);

namespace Districts\Editor\UI\Controller;

use Districts\Editor\Application\CityIterator;
use Districts\Editor\UI\Session;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig as View;

final readonly class AddFormController
{
    public function __construct(
        private CityIterator $cityIterator,
        private Session $session,
        private View $view,
    ) {
    }

    /**
     * @param array<string, string> $args
     */
    // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $cities = iterator_to_array($this->cityIterator);
        $templateData = [
            "title" => "Add a district",
            "cities" => $cities,
            "district" => $this->session->getAndDelete("form.add.values"),
            "errors" => $this->session->getAndDelete("form.add.errors"),
            "errorMessage" => $this->session->getAndDelete("form.add.error.message"),
        ];
        return $this->view->render($response, "add.html", $templateData);
    }
}
