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

    public function validate(array $data): ValidationResult
    {
        $result = $this->districtValidator->validate($data);
        if (!$this->validateCity($data)) {
            $result->addError("city");
        }
        return $result;
    }

    private function validateCity(array $data): bool
    {
        if (!array_key_exists("city", $data)) {
            return false;
        }
        if (!in_array($data["city"], $this->validCityIds, true)) {
            return false;
        }
        return true;
    }
}
