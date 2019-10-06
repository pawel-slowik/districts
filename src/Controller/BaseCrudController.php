<?php

declare(strict_types=1);

namespace Controller;

use Slim\Views\Twig as View;
use Repository\DistrictRepository;

abstract class BaseCrudController
{
    protected $repository;

    protected $view;

    public function __construct(DistrictRepository $repository, View $view)
    {
        $this->repository = $repository;
        $this->view = $view;
    }
}
