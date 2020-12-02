<?php

declare(strict_types=1);

namespace Districts\UI\Web\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpNotFoundException;

use Districts\UI\Web\Redirector;
use Districts\UI\Web\Factory\RemoveDistrictCommandFactory;

use Districts\Service\DistrictService;
use Districts\Service\NotFoundException;

final class RemoveActionController
{
    private $districtService;

    private $commandFactory;

    private $redirector;

    public function __construct(
        DistrictService $districtService,
        RemoveDistrictCommandFactory $commandFactory,
        Redirector $redirector
    ) {
        $this->districtService = $districtService;
        $this->commandFactory = $commandFactory;
        $this->redirector = $redirector;
    }

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        try {
            $this->districtService->remove($this->commandFactory->fromRequest($request, $args));
            // TODO: flash message
        } catch (NotFoundException $exception) {
            throw new HttpNotFoundException($request);
        }
        return $this->redirector->redirect($request, $response, "list");
    }
}
