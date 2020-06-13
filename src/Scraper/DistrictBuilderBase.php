<?php

declare(strict_types=1);

namespace Scraper;

use Entity\District;
use Validator\Validator;

abstract class DistrictBuilderBase
{
    protected function createValidatedDistrict(Validator $validator, array $data): District
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
