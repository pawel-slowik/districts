<?php

declare(strict_types=1);

namespace UI\CLI;

use Repository\ProgressReporter;

use Symfony\Component\Console\Helper\ProgressBar;

class ProgressBarProgressReporter implements ProgressReporter
{
    private $progressBar;

    public function __construct(ProgressBar $progressBar)
    {
        $this->progressBar = $progressBar;
    }

    public function advance(): void
    {
        $this->progressBar->advance();
    }
}
