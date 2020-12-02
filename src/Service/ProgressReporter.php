<?php

declare(strict_types=1);

namespace Districts\Service;

interface ProgressReporter
{
    public function advance(): void;
}
