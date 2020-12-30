<?php

declare(strict_types=1);

namespace Districts\UI\CLI;

use Districts\Application\ProgressReporter;

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
