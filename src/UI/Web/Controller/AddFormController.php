<?php

declare(strict_types=1);

namespace Districts\UI\Web\Controller;

use Districts\Application\CityIterator;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig as View;
use SlimSession\Helper as Session;

final class AddFormController
{
    public function __construct(
        private CityIterator $cityIterator,
        private Session $session,
        private View $view,
    ) {
    }

    // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $cities = iterator_to_array($this->cityIterator);
        $templateData = [
            "title" => "Add a district",
            "cities" => $cities,
            "district" => $this->session->get("form.add.values"),
            "errors" => $this->session->get("form.add.errors"),
            "errorMessage" => $this->session->get("form.add.error.message"),
        ];
        $this->session->delete("form.add.error.message");
        $this->session->delete("form.add.values");
        $this->session->delete("form.add.errors");
        return $this->view->render($response, "add.html", $templateData);
    }
}
