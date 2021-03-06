<?php

declare(strict_types=1);

namespace Districts\UI\Web\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpNotFoundException;
use SlimSession\Helper as Session;

use Districts\UI\Web\Redirector;
use Districts\UI\Web\Factory\RemoveDistrictCommandFactory;

use Districts\Application\DistrictService;
use Districts\Application\NotFoundException as ApplicationNotFoundException;
use Districts\DomainModel\NotFoundException as DomainNotFoundException;

final class RemoveActionController
{
    private $districtService;

    private $commandFactory;

    private $session;

    private $redirector;

    public function __construct(
        DistrictService $districtService,
        RemoveDistrictCommandFactory $commandFactory,
        Session $session,
        Redirector $redirector
    ) {
        $this->districtService = $districtService;
        $this->commandFactory = $commandFactory;
        $this->session = $session;
        $this->redirector = $redirector;
    }

    // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        try {
            $removed = $this->districtService->remove($this->commandFactory->fromRequest($request, $args));
            if ($removed) {
                $this->session["success.message"] = "District data removed.";
            }
        } catch (DomainNotFoundException | ApplicationNotFoundException $exception) {
            throw new HttpNotFoundException($request);
        }
        return $this->redirector->redirect($request->getUri(), "list");
    }
}
