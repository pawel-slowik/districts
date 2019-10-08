<?php

declare(strict_types=1);

namespace Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\EntityManager;

use Entity\City;

use Scraper\HtmlFinder;
use Scraper\GuzzleHtmlFetcher;
use Scraper\City\GdanskScraper;
use Scraper\City\KrakowScraper;

class UpdateCommand extends Command
{
    protected $entityManager;

    protected $scrapers;

    public function __construct(EntityManager $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $finder = new HtmlFinder();
        $fetcher = new GuzzleHtmlFetcher();
        $this->scrapers = [
            new GdanskScraper($fetcher, $finder),
            new KrakowScraper($fetcher, $finder),
        ];
        $this->setName("update");
        $this->setDescription("Update the districts database with scraped data. Overwrites all changes.");
    }

    // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        foreach ($this->scrapers as $scraper) {
            $cityName = $scraper->getCityName();
            $districts = $scraper->listDistricts();
            $this->updateCity($cityName, $districts);
        }
    }

    protected function updateCity(string $cityName, iterable $districts): void
    {
        $city = $this->findCity($cityName);
        if ($city) {
            foreach ($city->listDistricts() as $district) {
                $this->entityManager->remove($district);
            }
            $this->entityManager->flush();
        } else {
            $city = new City($cityName);
        }
        foreach ($districts as $district) {
            $city->addDistrict($district);
            $district->setCity($city);
            $this->entityManager->persist($district);
        }
        $this->entityManager->persist($city);
        $this->entityManager->flush();
    }

    protected function findCity(string $cityName): ?City
    {
        $cityRepository = $this->entityManager->getRepository(City::class);
        $city = $cityRepository->findOneBy(["name" => $cityName]);
        return $city;
    }
}
