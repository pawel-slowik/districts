<?php

declare(strict_types=1);

namespace Validator;

final class NewDistrictValidator
{
    private $validCityIds;

    private $districtValidator;

    public function __construct(DistrictValidator $districtValidator, array $validCityIds)
    {
        $this->districtValidator = $districtValidator;
        $this->validCityIds = $validCityIds;
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
        if (!in_array($city, $this->validCityIds, true)) {
            return false;
        }
        return true;
    }
}
