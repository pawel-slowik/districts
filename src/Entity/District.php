<?php

declare(strict_types=1);

namespace Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *   name="districts",
 *   uniqueConstraints={@ORM\Uniqueconstraint(columns={"city_id", "name"})},
 *   options={"collate"="utf8_polish_ci"}
 * )
 */
class District
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
     * @ORM\Column(type="float")
     */
    private $area;

    /**
     * @ORM\Column(type="integer")
     */
    private $population;

    /**
     * @ORM\ManyToOne(targetEntity="City", inversedBy="districts")
     */
    private $city;

    public function __construct(string $name, float $area, int $population)
    {
        $this->name = $name;
        $this->area = $area;
        $this->population = $population;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getArea(): float
    {
        return $this->area;
    }

    public function getPopulation(): int
    {
        return $this->population;
    }

    public function getCity(): ?City
    {
        return $this->city;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function setArea(float $area): void
    {
        $this->area = $area;
    }

    public function setPopulation(int $population): void
    {
        $this->population = $population;
    }

    public function setCity(City $city): void
    {
        $this->city = $city;
    }
}
