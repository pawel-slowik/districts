<?php

declare(strict_types=1);

namespace Districts\Scraper\UI;

use Districts\Scraper\Domain\ProgressReporter;
use Symfony\Component\Console\Helper\ProgressBar;

final readonly class ProgressBarProgressReporter implements ProgressReporter
{
    public function __construct(
        private ProgressBar $progressBar,
    ) {
    }

    public function setTotal(int $total): void
    {
        $this->progressBar->setMaxSteps($total);
    }

    public function advance(): void
    {
        $this->progressBar->advance();
    }
}
