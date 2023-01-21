<?php

declare(strict_types=1);

namespace Districts\Scraper\UI;

use Districts\Scraper\Application\ProgressReporter;
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
