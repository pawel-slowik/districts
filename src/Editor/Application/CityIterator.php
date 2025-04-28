<?php

declare(strict_types=1);

namespace Districts\Editor\Application;

use ArrayIterator;
use Districts\Core\Domain\City;
use Districts\Core\Domain\CityRepository;
use IteratorAggregate;
use Traversable;

/**
 * @implements IteratorAggregate<City>
 */
final readonly class CityIterator implements IteratorAggregate
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
