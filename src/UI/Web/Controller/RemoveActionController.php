<?php

declare(strict_types=1);

namespace Districts\UI\Web\Controller;

use Districts\Application\DistrictService;
use Districts\Application\Exception\NotFoundException;
use Districts\DomainModel\Exception\DistrictNotFoundException;
use Districts\UI\Web\Factory\RemoveDistrictCommandFactory;
use Districts\UI\Web\ReverseRouter;
use Fig\Http\Message\StatusCodeInterface as StatusCode;
use Nyholm\Psr7\Response as NyholmResponse;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpNotFoundException;
use SlimSession\Helper as Session;

final class RemoveActionController
{
    private DistrictService $districtService;

    private RemoveDistrictCommandFactory $commandFactory;

    private Session $session;

    private ReverseRouter $reverseRouter;

    public function __construct(
        DistrictService $districtService,
        RemoveDistrictCommandFactory $commandFactory,
        Session $session,
        ReverseRouter $reverseRouter
    ) {
        $this->districtService = $districtService;
        $this->commandFactory = $commandFactory;
        $this->session = $session;
        $this->reverseRouter = $reverseRouter;
    }

    // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $command = $this->commandFactory->fromRoute($args);
        try {
            $this->districtService->remove($command);
            $this->session["success.message"] = "District data removed.";
        } catch (DistrictNotFoundException | NotFoundException $exception) {
            throw new HttpNotFoundException($request);
        }
        $url = $this->reverseRouter->urlFromRoute($request->getUri(), "list");
        return (new NyholmResponse())->withHeader("Location", $url)->withStatus(StatusCode::STATUS_FOUND);
    }
}
