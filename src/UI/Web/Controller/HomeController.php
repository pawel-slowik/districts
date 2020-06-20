<?php

declare(strict_types=1);

namespace UI\Web\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use UI\Web\Redirector;

final class HomeController
{
    private $redirector;

    public function __construct(Redirector $redirector)
    {
        $this->redirector = $redirector;
    }

    // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        return $this->redirector->redirect($request, $response, "list");
    }
}
