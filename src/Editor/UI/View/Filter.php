<?php

declare(strict_types=1);

namespace Districts\Editor\UI\View;

readonly class Filter
{
    public function __construct(
        public ?string $column,
        public ?string $value,
    ) {
    }
}
