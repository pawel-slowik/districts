<?php

declare(strict_types=1);

namespace Districts\Editor\UI\Factory;

use Districts\Domain\Pagination;

class PaginationFactory
{
    public function createFromRequestInput(?string $pageNumber): Pagination
    {
        $validPageNumber = filter_var(
            $pageNumber,
            FILTER_VALIDATE_INT,
            [
                "options" => [
                    "default" => 1,
                    "min_range" => 1,
                ],
            ]
        );
        return new Pagination($validPageNumber, 20);
    }
}
