<?php

declare(strict_types=1);

namespace Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressBar;

use Service\DistrictService;

use Scraper\HtmlFinder;
use Scraper\GuzzleHtmlFetcher;
use Scraper\City\GdanskScraper;
use Scraper\City\KrakowScraper;

final class UpdateCommand extends Command
{
    private $districtService;

    private $scrapers;

    public function __construct(DistrictService $districtService)
    {
        parent::__construct();
        $this->districtService = $districtService;
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
        try {
            $cityFilter = new ScraperCityFilter($this->scrapers, $input->getArgument("city_names"));
        } catch (\InvalidArgumentException $ex) {
            throw new InvalidArgumentException($ex->getMessage(), $ex->getCode());
        }
        foreach ($this->scrapers as $scraper) {
            if (!$cityFilter->matches($scraper->getCityName())) {
                continue;
            }
            $this->updateCity($scraper->getCityName(), $scraper->listDistricts(), $output);
        }
    }

    private function updateCity(string $cityName, iterable $districts, OutputInterface $output): void
    {
        $output->writeln("processing city: " . $cityName);
        $progressBar = new ProgressBar($output);
        $progressBar->start();
        $this->districtService->setDistrictsForCityName(
            $cityName,
            $districts,
            new ProgressBarProgressReporter($progressBar),
        );
        $progressBar->finish();
        $output->writeln("");
    }
}
