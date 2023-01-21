<?php

declare(strict_types=1);

namespace Districts\Test\Scraper\UI;

use Districts\Scraper\Application\Importer;
use Districts\Scraper\Application\ProgressReporter;
use Districts\Scraper\Domain\CityDTO;
use Districts\Scraper\Domain\CityScraper;
use Districts\Scraper\UI\ImportCommand;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @covers \Districts\Scraper\UI\ImportCommand
 */
class ImportCommandTest extends TestCase
{
    /** @var Importer&MockObject */
    private Importer $importer;

    /** @var CityScraper&MockObject */
    private CityScraper $scraper;

    private ImportCommand $command;

    /** @var InputInterface&MockObject */
    private InputInterface $input;

    /** @var OutputInterface&MockObject */
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
