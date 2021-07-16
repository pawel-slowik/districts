<?php

declare(strict_types=1);

namespace Districts\UI\Web;

use Districts\UI\Web\Controller\{
    AddActionController,
    AddFormController,
    EditActionController,
    EditFormController,
    HomeController,
    ListController,
    RemoveActionController,
    RemoveFormController,
};
use Slim\App;

class RoutingConfiguration
{
    public static function apply(App $app): App
    {
        $app->get("/", HomeController::class);
        $app->get("/list[/order/{column}/{direction}]", ListController::class)->setName("list");
        $app->get("/add", AddFormController::class)->setName("add");
        $app->post("/add", AddActionController::class);
        $app->get("/edit/{id}", EditFormController::class)->setName("edit");
        $app->post("/edit/{id}", EditActionController::class);
        $app->get("/remove/{id}", RemoveFormController::class)->setName("remove");
        $app->post("/remove/{id}", RemoveActionController::class);
        return $app;
    }
}
