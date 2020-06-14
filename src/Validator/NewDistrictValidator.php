<?php

declare(strict_types=1);

namespace Validator;

class NewDistrictValidator extends DistrictValidator implements Validator
{
    private $validCityIds;

    public function __construct(array $validCityIds)
    {
        $this->validCityIds = $validCityIds;
    }

    public function validate(array $data): ValidationResult
    {
        $result = parent::validate($data);
        $this->validateCity($data, $result);
        return $result;
    }

    protected function validateCity(array $data, ValidationResult $result): void
    {
        if (!array_key_exists("city", $data)) {
            $result->addError("city");
            return;
        }
        if (!in_array($data["city"], $this->validCityIds, true)) {
            $result->addError("city");
            return;
        }
    }
}
