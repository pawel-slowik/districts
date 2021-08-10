<?php

declare(strict_types=1);

namespace Districts\UI\Web\Controller;

use Districts\Application\DistrictService;
use Districts\Application\NotFoundException as ApplicationNotFoundException;
use Districts\DomainModel\Exception\NotFoundException as DomainNotFoundException;
use Districts\UI\Web\Factory\RemoveDistrictCommandFactory;
use Districts\UI\Web\Redirector;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpNotFoundException;
use SlimSession\Helper as Session;

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
        $command = $this->commandFactory->fromRequest($request, $args);
        if ($command->isConfirmed()) {
            try {
                $this->districtService->remove($command);
                $this->session["success.message"] = "District data removed.";
            } catch (DomainNotFoundException | ApplicationNotFoundException $exception) {
                throw new HttpNotFoundException($request);
            }
        }
        return $this->redirector->redirect($request->getUri(), "list");
    }
}
