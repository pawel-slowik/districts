<?php

declare(strict_types=1);

namespace Districts\Scraper\Application;

interface ProgressReporter
{
    public function advance(): void;
}
