<?php

declare(strict_types=1);

namespace Entity;

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

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function addDistrict(District $district): void
    {
        $this->districts[] = $district;
    }

    public function listDistricts(): iterable
    {
        return $this->districts;
    }
}
