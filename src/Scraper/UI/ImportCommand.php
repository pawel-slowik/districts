<?php

declare(strict_types=1);

namespace Districts\Scraper\UI;

use Districts\Scraper\Application\Importer;
use Districts\Scraper\Domain\CityDTO;
use Districts\Scraper\Domain\CityScraper;
use InvalidArgumentException as FilterInvalidArgumentException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class ImportCommand extends Command
{
    /**
     * @param Importer      $importer
     * @param CityScraper[] $scrapers
     */
    public function __construct(
        private Importer $importer,
        private array $scrapers,
    ) {
        parent::__construct();
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
        /** @var string[] $cityNames */
        $cityNames = $input->getArgument("city_names");
        try {
            $cityFilter = new ScraperCityFilter($cityNames);
            foreach ($cityFilter->filter($this->scrapers) as $scraper) {
                $this->updateCity($scraper->scrape(), $output);
            }
        } catch (FilterInvalidArgumentException $ex) {
            throw new InvalidArgumentException($ex->getMessage(), $ex->getCode());
        }
        return 0;
    }

    private function updateCity(CityDTO $cityDTO, OutputInterface $output): void
    {
        $output->writeln("processing city: " . $cityDTO->name);
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
