<?php

declare(strict_types=1);

namespace Districts\Application;

use Districts\DomainModel\Entity\City;

use Districts\Infrastructure\CityRepository;

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
