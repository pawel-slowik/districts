<?php

declare(strict_types=1);

namespace Districts\UI\CLI;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressBar;

use Districts\Application\Importer;

use Districts\Scraper\CityDTO;
use Districts\Scraper\HtmlFinder;
use Districts\Scraper\HtmlFetcher;
use Districts\Scraper\Gdansk\CityScraper as GdanskScraper;
use Districts\Scraper\Krakow\CityScraper as KrakowScraper;

final class UpdateCommand extends Command
{
    private $importer;

    private $fetcher;

    private $scrapers;

    public function __construct(Importer $importer, HtmlFetcher $fetcher)
    {
        parent::__construct();
        $this->importer = $importer;
        $this->fetcher = $fetcher;
        $finder = new HtmlFinder();
        $this->scrapers = [
            new GdanskScraper($this->fetcher, $finder),
            new KrakowScraper($this->fetcher, $finder),
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
