<?php

declare(strict_types=1);

namespace Districts\Scraper\Domain;

interface ProgressReporter
{
    public function setTotal(int $total): void;

    public function advance(): void;
}
