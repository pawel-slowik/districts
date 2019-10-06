<?php

declare(strict_types=1);

namespace Validator;

class NewDistrictValidator implements Validator
{
    protected $validCityIds;

    public function __construct(array $validCityIds)
    {
        $this->validCityIds = $validCityIds;
    }

    public function validate(array $data): ValidationResult
    {
        $result = new ValidationResult();
        $this->validateCity($data, $result);
        $this->validateName($data, $result);
        $this->validateArea($data, $result);
        $this->validatePopulation($data, $result);
        return $result;
    }

    protected function validateCity(array $data, ValidationResult $result): void
    {
        if (!array_key_exists("city", $data)) {
            $result->addError("city");
            return;
        }
        $city = intval($data["city"]);
        if (!in_array($city, $this->validCityIds, true)) {
            $result->addError("city");
            return;
        }
        $result->addValidatedData("city", $city);
    }

    protected function validateName(array $data, ValidationResult $result): void
    {
        if (!array_key_exists("name", $data)) {
            $result->addError("name");
            return;
        }
        $name = trim(strval($data["name"]));
        if ($name === "") {
            $result->addError("name");
            return;
        }
        $result->addValidatedData("name", $name);
    }

    protected function validateArea(array $data, ValidationResult $result): void
    {
        if (!array_key_exists("area", $data)) {
            $result->addError("area");
            return;
        }
        $area = floatval($data["area"]);
        if ($area <= 0) {
            $result->addError("area");
            return;
        }
        $result->addValidatedData("area", $area);
    }

    protected function validatePopulation(array $data, ValidationResult $result): void
    {
        if (!array_key_exists("population", $data)) {
            $result->addError("population");
            return;
        }
        $population = intval($data["population"]);
        if ($population <= 0) {
            $result->addError("population");
            return;
        }
        $result->addValidatedData("population", $population);
    }
}
