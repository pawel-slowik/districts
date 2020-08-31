<?php

declare(strict_types=1);

namespace Scraper\District;

use Validator\DistrictValidator;
use Scraper\RuntimeException;

abstract class BuilderBase
{
    /**
     * @param scalar $name
     * @param scalar $area
     * @param scalar $population
     */
    // phpcs:ignore PEAR.Commenting.FunctionComment.ParamNameNoMatch
    protected function createValidatedDistrictDTO(
        DistrictValidator $validator,
        $name,
        $area,
        $population
    ): DistrictDTO {
        $result = $validator->validate($name, $area, $population);
        if (!$result->isOk()) {
            throw new RuntimeException(
                "validation failed: " . implode(", ", array_map("strval", $result->getErrors()))
            );
        }
        /** @var string $name */
        /** @var float $area */
        /** @var int $population */
        return new DistrictDTO($name, $area, $population);
    }
}
