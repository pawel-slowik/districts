<?php

declare(strict_types=1);

namespace UI\CLI;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressBar;

use Service\Importer;

use Scraper\HtmlFinder;
use Scraper\GuzzleHtmlFetcher;
use Scraper\District\Gdansk\Scraper as GdanskScraper;
use Scraper\District\Krakow\Scraper as KrakowScraper;

final class UpdateCommand extends Command
{
    private $importer;

    private $scrapers;

    public function __construct(Importer $importer)
    {
        parent::__construct();
        $this->importer = $importer;
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

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $cityFilter = new ScraperCityFilter($this->scrapers, $input->getArgument("city_names"));
        } catch (\InvalidArgumentException $ex) {
            throw new InvalidArgumentException($ex->getMessage(), $ex->getCode());
        }
        foreach ($cityFilter->filter($this->scrapers) as $scraper) {
            $this->updateCity($scraper->getCityName(), $scraper->listDistricts(), $output);
        }
        return 0;
    }

    private function updateCity(string $cityName, iterable $districts, OutputInterface $output): void
    {
        $output->writeln("processing city: " . $cityName);
        $progressBar = new ProgressBar($output);
        $progressBar->start();
        $this->importer->import(
            $cityName,
            $districts,
            new ProgressBarProgressReporter($progressBar),
        );
        $progressBar->finish();
        $output->writeln("");
    }
}
