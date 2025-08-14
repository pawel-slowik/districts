<?php

declare(strict_types=1);

namespace Districts\Core\Domain;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "districts")]
#[ORM\UniqueConstraint(name: "districts_city_id_name", columns: ["city_id", "name"])]
class District
{
    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    #[ORM\GeneratedValue]
    private int $id;

    #[ORM\Embedded(class: Name::class, columnPrefix: false)]
    private Name $name;

    #[ORM\Embedded(class: Area::class, columnPrefix: false)]
    private Area $area;

    #[ORM\Embedded(class: Population::class, columnPrefix: false)]
    private Population $population;

    #[ORM\ManyToOne(targetEntity: City::class, inversedBy: "districts")]
    #[ORM\JoinColumn(nullable: false)]
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

    public function update(Name $name, Area $area, Population $population): void
    {
        $this->name = $name;
        $this->area = $area;
        $this->population = $population;
    }
}
