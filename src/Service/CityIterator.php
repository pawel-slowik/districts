<?php

declare(strict_types=1);

namespace Districts\Service;

use Districts\DomainModel\Entity\City;

use Districts\Repository\CityRepository;

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
