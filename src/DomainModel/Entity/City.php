<?php

declare(strict_types=1);

namespace Districts\DomainModel\Entity;

use Districts\Service\NotFoundException;
use Districts\Service\ValidationException;
use Districts\Validator\DistrictValidator;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(
 *   name="cities",
 *   uniqueConstraints={@ORM\Uniqueconstraint(columns={"name"})},
 *   options={"collate"="utf8_polish_ci"}
 * )
 */
class City
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="District", mappedBy="city")
     */
    private $districts;

    public function __construct(string $name)
    {
        $this->name = $name;
        $this->districts = new ArrayCollection();
    }

    public function addDistrict(
        DistrictValidator $districtValidator,
        string $name,
        float $area,
        int $population
    ): District {
        $validationResult = $districtValidator->validate($this->id, $name, $area, $population);
        if (!$validationResult->isOk()) {
            throw (new ValidationException())->withErrors($validationResult->getErrors());
        }
        $district = new District($this, $name, $area, $population);
        $this->districts[] = $district;
        return $district;
    }

    public function updateDistrict(
        DistrictValidator $districtValidator,
        int $districtId,
        string $name,
        float $area,
        int $population
    ): District {
        $validationResult = $districtValidator->validate($this->id, $name, $area, $population);
        if (!$validationResult->isOk()) {
            throw (new ValidationException())->withErrors($validationResult->getErrors());
        }
        $district = $this->getDistrictById($districtId);
        $district->setName($name);
        $district->setArea($area);
        $district->setPopulation($population);
        return $district;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function listDistricts(): iterable
    {
        return $this->districts;
    }

    private function getDistrictById(int $districtId): District
    {
        foreach ($this->districts as $district) {
            if ($district->getId() === $districtId) {
                return $district;
            }
        }

        throw new NotFoundException();
    }
}
