<?php

declare(strict_types=1);

namespace Repository;

interface ProgressReporter
{
    public function advance(): void;
}
