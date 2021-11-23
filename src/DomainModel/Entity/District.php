<?php

declare(strict_types=1);

namespace Districts\DomainModel\Entity;

use Districts\DomainModel\VO\Area;
use Districts\DomainModel\VO\Name;
use Districts\DomainModel\VO\Population;
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
    private int $id;

    /**
     * @ORM\Embedded(class="\Districts\DomainModel\VO\Name", columnPrefix=false)
     */
    private Name $name;

    /**
     * @ORM\Embedded(class="\Districts\DomainModel\VO\Area", columnPrefix=false)
     */
    private Area $area;

    /**
     * @ORM\Embedded(class="\Districts\DomainModel\VO\Population", columnPrefix=false)
     */
    private Population $population;

    /**
     * @ORM\ManyToOne(targetEntity="City", inversedBy="districts")
     */
    private City $city;

    public function __construct(City $city, Name $name, Area $area, Population $population)
    {
        $this->city = $city;
        $this->name = $name;
        $this->area = $area;
        $this->population = $population;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): Name
    {
        return $this->name;
    }

    public function getArea(): Area
    {
        return $this->area;
    }

    public function getPopulation(): Population
    {
        return $this->population;
    }

    public function getCity(): City
    {
        return $this->city;
    }

    public function setName(Name $name): void
    {
        $this->name = $name;
    }

    public function setArea(Area $area): void
    {
        $this->area = $area;
    }

    public function setPopulation(Population $population): void
    {
        $this->population = $population;
    }
}
