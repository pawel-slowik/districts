<?php

declare(strict_types=1);

namespace Districts\Infrastructure;

use Doctrine\Common\Cache\Cache;
use Psr\SimpleCache\CacheInterface;
use Psr\SimpleCache\InvalidArgumentException;

final class DoctrinePsrCache implements Cache
{
    private CacheInterface $psrCache;

    public function __construct(CacheInterface $psrCache)
    {
        $this->psrCache = $psrCache;
    }

    public function fetch($id)
    {
        try {
            return $this->psrCache->get($id, false);
        } catch (InvalidArgumentException $exception) {
            return false;
        }
    }

    public function contains($id): bool
    {
        try {
            return $this->psrCache->has($id);
        } catch (InvalidArgumentException $exception) {
            return false;
        }
    }

    public function save($id, $data, $lifeTime = 0): bool
    {
        try {
            return $this->psrCache->set($id, $data, $lifeTime);
        } catch (InvalidArgumentException $exception) {
            return false;
        }
    }

    public function delete($id): bool
    {
        try {
            return $this->psrCache->delete($id);
        } catch (InvalidArgumentException $exception) {
            return false;
        }
    }

    public function getStats()
    {
        return null;
    }
}
