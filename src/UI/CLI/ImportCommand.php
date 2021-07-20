<?php

declare(strict_types=1);

namespace Districts\UI\CLI;

use Districts\Application\Importer;
use Districts\DomainModel\Scraper\CityDTO;
use Districts\DomainModel\Scraper\CityScraper;
use InvalidArgumentException as FilterInvalidArgumentException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class ImportCommand extends Command
{
    private $importer;

    private $scrapers;

    /**
     * @param Importer      $importer
     * @param CityScraper[] $scrapers
     */
    public function __construct(Importer $importer, array $scrapers)
    {
        parent::__construct();
        $this->importer = $importer;
        $this->scrapers = $scrapers;
        $this->setName("import");
        $this->setDescription("Scrape and save districts data. Overwrites existing records.");
        $this->addArgument(
            "city_names",
            InputArgument::OPTIONAL | InputArgument::IS_ARRAY,
            "List of city names to import (default: all cities)"
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $cityFilter = new ScraperCityFilter($this->scrapers, $input->getArgument("city_names"));
        } catch (FilterInvalidArgumentException $ex) {
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
