<?php

declare(strict_types=1);

namespace Districts\UI\Web\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig as View;
use SlimSession\Helper as Session;

use Districts\Application\CityIterator;

final class AddFormController
{
    private $cityIterator;

    private $session;

    private $view;

    public function __construct(
        CityIterator $cityIterator,
        Session $session,
        View $view
    ) {
        $this->cityIterator = $cityIterator;
        $this->session = $session;
        $this->view = $view;
    }

    // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $cities = iterator_to_array($this->cityIterator);
        $templateData = [
            "title" => "Add a district",
            "cities" => $cities,
            "district" => $this->session["form.add.values"],
            "errors" => $this->session["form.add.errors"],
            "errorMessage" => $this->session["form.add.error.message"],
        ];
        unset($this->session["form.add.error.message"]);
        unset($this->session["form.add.values"]);
        unset($this->session["form.add.errors"]);
        return $this->view->render($response, "add.html", $templateData);
    }
}
