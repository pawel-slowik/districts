<?php

declare(strict_types=1);

namespace Scraper\District;

use DomainModel\Entity\District;
use Validator\DistrictValidator;
use Scraper\RuntimeException;

abstract class BuilderBase
{
    protected function createValidatedDistrict(DistrictValidator $validator, array $data): District
    {
        $result = $validator->validate($data);
        if (!$result->isOk()) {
            throw new RuntimeException(
                "validation failed: " . implode(", ", array_map("strval", $result->getErrors()))
            );
        }
        return new District($data["name"], $data["area"], $data["population"]);
    }
}
