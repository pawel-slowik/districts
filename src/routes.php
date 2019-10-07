<?php

declare(strict_types=1);

use Slim\App;
use Controller\ListController;
use Controller\AddFormController;
use Controller\AddActionController;
use Controller\EditFormController;
use Controller\EditActionController;
use Controller\RemoveFormController;
use Controller\RemoveActionController;

return function (App $app): void {
    // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
    $app->get("/", function ($request, $response, $args) {
        return $response->withRedirect("/list");
    });
    $app->get("/list[/order/{column}/{direction}]", ListController::class)->setName("list");
    $app->get("/add", AddFormController::class)->setName("add");
    $app->post("/add", AddActionController::class);
    $app->get("/edit/{id}", EditFormController::class)->setName("edit");
    $app->post("/edit/{id}", EditActionController::class);
    $app->get("/remove/{id}", RemoveFormController::class)->setName("remove");
    $app->post("/remove/{id}", RemoveActionController::class);
};
