<?php

declare(strict_types=1);

namespace Districts\Test\UI\CLI;

use Districts\Application\Importer;
use Districts\Application\ProgressReporter;
use Districts\DomainModel\Scraper\CityDTO;
use Districts\DomainModel\Scraper\CityScraper;
use Districts\UI\CLI\ImportCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @covers \Districts\UI\CLI\ImportCommand
 */
class ImportCommandTest extends TestCase
{
    private Importer $importer;

    private CityScraper $scraper;

    private ImportCommand $command;

    private InputInterface $input;

    private OutputInterface $output;

    protected function setUp(): void
    {
        $this->importer = $this->createMock(Importer::class);
        $this->scraper = $this->createMock(CityScraper::class);
        $this->input = $this->createMock(InputInterface::class);
        $this->output = $this->createMock(OutputInterface::class);
        // quiet to avoid the need to mock output helpers
        $this->output->method("getVerbosity")->willReturn(OutputInterface::VERBOSITY_QUIET);

        $this->command = new ImportCommand(
            $this->importer,
            [$this->scraper]
        );
    }

    public function testInvalidCityName(): void
    {
        $this->input
            ->method("getArgument")
            ->with($this->identicalTo("city_names"))
            ->willReturn(["Foo"]);

        $this->scraper
            ->method("getCityName")
            ->willReturn("Bar");

        $this->expectException(InvalidArgumentException::class);
        $this->command->run($this->input, $this->output);
    }

    public function testValidCityName(): void
    {
        $this->input
            ->method("getArgument")
            ->with($this->identicalTo("city_names"))
            ->willReturn([]);

        $this->importer
            ->expects($this->once())
            ->method("import")
            ->with(
                $this->isInstanceOf(CityDTO::class),
                $this->isInstanceOf(ProgressReporter::class),
            );

        $this->command->run($this->input, $this->output);
    }
}
