<?php

declare(strict_types=1);

use Slim\App;
use Controller\ListController;
use Controller\AddController;
use Controller\EditController;
use Controller\RemoveController;

return function (App $app): void {
    $app->get("/list[/order/{column}/{direction}]", ListController::class)->setName("list");
    $app->get("/add", AddController::class)->setName("add");
    $app->get("/edit/{id}", EditController::class)->setName("edit");
    $app->get("/remove/{id}", RemoveController::class)->setName("remove");
};
