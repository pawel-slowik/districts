<?php

declare(strict_types=1);

namespace Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressBar;

use Entity\City;
use Repository\CityRepository;
use Repository\DistrictRepository;

use Scraper\HtmlFinder;
use Scraper\GuzzleHtmlFetcher;
use Scraper\City\GdanskScraper;
use Scraper\City\KrakowScraper;

final class UpdateCommand extends Command
{
    private $cityRepository;

    private $districtRepository;

    private $scrapers;

    public function __construct(
        CityRepository $cityRepository,
        DistrictRepository $districtRepository
    ) {
        parent::__construct();
        $this->cityRepository = $cityRepository;
        $this->districtRepository = $districtRepository;
        $finder = new HtmlFinder();
        $fetcher = new GuzzleHtmlFetcher();
        $this->scrapers = [
            new GdanskScraper($fetcher, $finder),
            new KrakowScraper($fetcher, $finder),
        ];
        $this->setName("update");
        $this->setDescription("Update the districts database with scraped data. Overwrites existing records.");
        $this->addArgument(
            "city_names",
            InputArgument::OPTIONAL | InputArgument::IS_ARRAY,
            "List of city names to update (default: update all cities)"
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $inputCityNames = $input->getArgument("city_names");
        if (!$this->checkCityFilter($inputCityNames)) {
            throw new InvalidArgumentException("unsupported city filter: " . var_export($inputCityNames, true));
        }
        foreach ($this->scrapers as $scraper) {
            $cityName = $scraper->getCityName();
            if (!$this->cityFilterMatches($cityName, $inputCityNames)) {
                continue;
            }
            $districts = $scraper->listDistricts();
            $this->updateCity($cityName, $districts, $output);
        }
    }

    private function updateCity(string $cityName, iterable $districts, OutputInterface $output): void
    {
        $output->writeln("processing city: " . $cityName);
        $city = $this->cityRepository->findByName($cityName);
        if ($city) {
            $this->districtRepository->removeMultiple($city->listDistricts());
        } else {
            $city = new City($cityName);
            $this->cityRepository->add($city);
        }
        $progressBar = new ProgressBar($output);
        $progressBar->start();
        $this->districtRepository->addMultiple(
            $this->prepareDistricts($districts, $city),
            new ProgressBarProgressReporter($progressBar)
        );
        $progressBar->finish();
        $output->writeln("");
    }

    private function prepareDistricts(iterable $districts, City $city): iterable
    {
        foreach ($districts as $district) {
            $city->addDistrict($district);
            $district->setCity($city);
            yield $district;
        }
    }

    private function cityFilterMatches(string $cityName, array $cityNameFilter): bool
    {
        return empty($cityNameFilter) || in_array($cityName, $cityNameFilter, true);
    }

    private function checkCityFilter(array $cityNameFilter): bool
    {
        $supportedCityNames = array_map(
            function ($scraper) {
                return $scraper->getCityName();
            },
            $this->scrapers
        );
        return empty(array_diff($cityNameFilter, $supportedCityNames));
    }
}
