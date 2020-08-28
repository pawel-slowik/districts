<?php

declare(strict_types=1);

namespace Validator;

class NewDistrictValidator extends DistrictValidator
{
    private $validCityIds;

    public function __construct(array $validCityIds)
    {
        $this->validCityIds = $validCityIds;
    }

    public function validate(array $data): ValidationResult
    {
        $result = parent::validate($data);
        if (!$this->validateCity($data)) {
            $result->addError("city");
        }
        return $result;
    }

    protected function validateCity(array $data): bool
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
