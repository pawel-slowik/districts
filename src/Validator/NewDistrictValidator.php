<?php

declare(strict_types=1);

namespace Validator;

use Service\CityIterator;

final class NewDistrictValidator
{
    private $districtValidator;

    private $cityIterator;

    public function __construct(DistrictValidator $districtValidator, CityIterator $cityIterator)
    {
        $this->districtValidator = $districtValidator;
        $this->cityIterator = $cityIterator;
    }

    /**
     * @param scalar $city
     * @param scalar $name
     * @param scalar $area
     * @param scalar $population
     */
    public function validate($city, $name, $area, $population): ValidationResult
    {
        $result = $this->districtValidator->validate($name, $area, $population);
        if (!$this->validateCity($city)) {
            $result->addError("city");
        }
        return $result;
    }

    /**
     * @param scalar $city
     */
    private function validateCity($city): bool
    {
        foreach ($this->cityIterator as $existingCity) {
            if ($existingCity->getId() === $city) {
                return true;
            }
        }
        return false;
    }
}
