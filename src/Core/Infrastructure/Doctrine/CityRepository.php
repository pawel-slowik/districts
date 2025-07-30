<?php

declare(strict_types=1);

namespace Districts\Core\Infrastructure\Doctrine;

use Districts\Core\Domain\City;
use Districts\Core\Domain\CityRepository as CityRepositoryInterface;
use Districts\Core\Infrastructure\NotFoundInRepositoryException;
use Doctrine\ORM\EntityManager;

final readonly class CityRepository implements CityRepositoryInterface
{
    public function __construct(
        private EntityManager $entityManager,
    ) {
    }

    public function get(int $id): City
    {
        $city = $this->entityManager->getRepository(City::class)->find($id);
        if ($city === null) {
            throw new NotFoundInRepositoryException();
        }
        return $city;
    }

    public function findByName(string $name): ?City
    {
        return $this->entityManager->getRepository(City::class)->findOneBy(["name" => $name]);
    }

    /**
     * @return City[]
     */
    public function list(): array
    {
        return $this->entityManager->getRepository(City::class)->findAll();
    }

    public function add(City $city): void
    {
        $this->entityManager->persist($city);
        $this->entityManager->flush();
    }

    public function update(City $city): void
    {
        $this->entityManager->persist($city);
        $this->entityManager->flush();
    }
}
