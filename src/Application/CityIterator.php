<?php

declare(strict_types=1);

namespace Districts\Application;

use Districts\DomainModel\CityRepository;
use Districts\DomainModel\Entity\City;

/**
 * @implements \IteratorAggregate<City>
 */
class CityIterator implements \IteratorAggregate
{
    private $cityRepository;

    public function __construct(
        CityRepository $cityRepository
    ) {
        $this->cityRepository = $cityRepository;
    }

    /**
     * @return \Traversable<City>
     */
    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->cityRepository->list());
    }
}
