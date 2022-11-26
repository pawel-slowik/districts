<?php

declare(strict_types=1);

namespace Districts\Domain;

use Districts\Domain\Exception\DistrictNotFoundException;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "cities", options: ["collate" => "utf8_polish_ci"])]
#[ORM\UniqueConstraint(name: "cities_name", columns: ["name"])]
class City
{
    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    #[ORM\GeneratedValue]
    private int $id;

    #[ORM\Column(type: "string")]
    private string $name;

    /** @var Collection<District> */
    #[ORM\OneToMany(targetEntity: District::class, mappedBy: "city", cascade: ["persist"], orphanRemoval: true)]
    private Collection $districts;

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
