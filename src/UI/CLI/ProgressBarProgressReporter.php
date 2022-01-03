<?php

declare(strict_types=1);

namespace Districts\UI\CLI;

use Districts\Application\ProgressReporter;
use Symfony\Component\Console\Helper\ProgressBar;

class ProgressBarProgressReporter implements ProgressReporter
{
    public function __construct(
        private ProgressBar $progressBar,
    ) {
    }

    public function advance(): void
    {
        $this->progressBar->advance();
    }
}
