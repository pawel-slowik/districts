<?php

declare(strict_types=1);

use Slim\App;
use Controller\ListController;
use Controller\AddFormController;
use Controller\AddActionController;
use Controller\EditController;
use Controller\RemoveFormController;
use Controller\RemoveActionController;

return function (App $app): void {
    $app->get("/list[/order/{column}/{direction}]", ListController::class)->setName("list");
    $app->get("/add", AddFormController::class)->setName("add");
    $app->post("/add", AddActionController::class);
    $app->get("/edit/{id}", EditController::class)->setName("edit");
    $app->get("/remove/{id}", RemoveFormController::class)->setName("remove");
    $app->post("/remove/{id}", RemoveActionController::class);
};
