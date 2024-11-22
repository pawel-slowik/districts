<?php

declare(strict_types=1);

namespace Districts\Editor\UI;

use Districts\Editor\UI\Controller\AddActionController;
use Districts\Editor\UI\Controller\AddFormController;
use Districts\Editor\UI\Controller\EditActionController;
use Districts\Editor\UI\Controller\EditFormController;
use Districts\Editor\UI\Controller\HomeController;
use Districts\Editor\UI\Controller\ListController;
use Districts\Editor\UI\Controller\RemoveActionController;
use Districts\Editor\UI\Controller\RemoveFormController;
use Psr\Container\ContainerInterface;
use Slim\App;

class RoutingConfiguration
{
    /**
     * @param App<ContainerInterface> $app
     */
    public static function apply(App $app): void
    {
        $app->get("/", HomeController::class);
        $app->get("/list[/order/{column}/{direction}]", ListController::class)->setName("list");
        $app->get("/add", AddFormController::class)->setName("add");
        $app->post("/add", AddActionController::class);
        $app->get("/edit/{id}", EditFormController::class)->setName("edit");
        $app->post("/edit/{id}", EditActionController::class);
        $app->get("/remove/{id}", RemoveFormController::class)->setName("remove");
        $app->post("/remove/{id}", RemoveActionController::class);
    }
}
