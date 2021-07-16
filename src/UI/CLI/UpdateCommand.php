<?php

declare(strict_types=1);

namespace Districts\UI\CLI;

use Districts\Application\Importer;
use Districts\DomainModel\Scraper\CityDTO;
use Districts\DomainModel\Scraper\Gdansk\CityScraper as GdanskScraper;
use Districts\DomainModel\Scraper\HtmlFetcher;
use Districts\DomainModel\Scraper\HtmlFinder;
use Districts\DomainModel\Scraper\Krakow\CityScraper as KrakowScraper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class UpdateCommand extends Command
{
    private $importer;

    private $scrapers;

    public function __construct(Importer $importer, HtmlFetcher $fetcher)
    {
        parent::__construct();
        $this->importer = $importer;
        $finder = new HtmlFinder();
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
            $this->updateCity($scraper->scrape(), $output);
        }
        return 0;
    }

    private function updateCity(CityDTO $cityDTO, OutputInterface $output): void
    {
        $output->writeln("processing city: " . $cityDTO->getName());
        $progressBar = new ProgressBar($output);
        $progressBar->start();
        $this->importer->import(
            $cityDTO,
            new ProgressBarProgressReporter($progressBar),
        );
        $progressBar->finish();
        $output->writeln("");
    }
}
