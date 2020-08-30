<?php

declare(strict_types=1);

namespace Service;

use DomainModel\Entity\City;

use Repository\CityRepository;

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
