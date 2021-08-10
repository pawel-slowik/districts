<?php

declare(strict_types=1);

namespace Districts\DomainModel\Entity;

use Districts\DomainModel\Exception\DistrictNotFoundException;
use Districts\DomainModel\VO\Area;
use Districts\DomainModel\VO\Name;
use Districts\DomainModel\VO\Population;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

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
     * @ORM\OneToMany(targetEntity="District", mappedBy="city", cascade={"persist"}, orphanRemoval=true)
     */
    private $districts;

    public function __construct(string $name)
    {
        $this->name = $name;
        $this->districts = new ArrayCollection();
    }

    public function addDistrict(
        Name $name,
        Area $area,
        Population $population
    ): void {
        $district = new District($this, $name, $area, $population);
        $this->districts[] = $district;
    }

    public function updateDistrict(
        int $districtId,
        Name $name,
        Area $area,
        Population $population
    ): void {
        $district = $this->getDistrictById($districtId);
        $district->setName($name);
        $district->setArea($area);
        $district->setPopulation($population);
    }

    public function removeDistrict(int $districtId): void
    {
        $district = $this->getDistrictById($districtId);
        $this->districts->removeElement($district);
    }

    public function removeAllDistricts(): void
    {
        $this->districts->clear();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    private function getDistrictById(int $districtId): District
    {
        foreach ($this->districts as $district) {
            if ($district->getId() === $districtId) {
                return $district;
            }
        }

        throw new DistrictNotFoundException();
    }
}
