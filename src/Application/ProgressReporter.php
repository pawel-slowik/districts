<?php

declare(strict_types=1);

namespace Districts\Application;

interface ProgressReporter
{
    public function advance(): void;
}
