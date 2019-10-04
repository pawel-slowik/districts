<?php

declare(strict_types=1);

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *   name="districts",
 *   uniqueConstraints={@ORM\Uniqueconstraint(columns={"city_id", "name"})}
 * )
 */
class District
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     */
    protected $name;

    /**
     * @ORM\Column(type="float")
     */
    protected $area;

    /**
     * @ORM\Column(type="integer")
     */
    protected $population;

    /**
    * @ORM\ManyToOne(targetEntity="City", inversedBy="districts")
    */
    protected $city;

    public function __construct(string $name, float $area, int $population)
    {
        $this->name = $name;
        $this->area = $area;
        $this->population = $population;
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

    public function setCity(City $city): void
    {
        $this->city = $city;
    }
}
