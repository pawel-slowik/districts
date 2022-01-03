<?php

declare(strict_types=1);

namespace Districts\Application;

use ArrayIterator;
use Districts\DomainModel\City;
use Districts\DomainModel\CityRepository;
use IteratorAggregate;
use Traversable;

/**
 * @implements IteratorAggregate<City>
 */
class CityIterator implements IteratorAggregate
{
    public function __construct(
        private CityRepository $cityRepository,
    ) {
    }

    /**
     * @return Traversable<City>
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->cityRepository->list());
    }
}
