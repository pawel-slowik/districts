<?php

declare(strict_types=1);

namespace Service;

interface ProgressReporter
{
    public function advance(): void;
}
