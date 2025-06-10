<?php

declare(strict_types=1);

namespace Districts\Test\Unit\Scraper\UI;

use Districts\Scraper\Application\Importer;
use Districts\Scraper\Application\ScraperCollection;
use Districts\Scraper\Domain\CityDTO;
use Districts\Scraper\Domain\CityScraper;
use Districts\Scraper\UI\ImportCommand;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Exception\InvalidArgumentException as SymfonyConsoleInvalidArgumentException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[CoversClass(ImportCommand::class)]
final class ImportCommandTest extends TestCase
{
    private Importer&MockObject $importer;

    private ScraperCollection&Stub $scraperCollection;

    private ImportCommand $command;

    private InputInterface&MockObject $input;

    private OutputInterface&MockObject $output;

    protected function setUp(): void
    {
        $this->importer = $this->createMock(Importer::class);
        $this->scraperCollection = $this->createStub(ScraperCollection::class);
        $this->input = $this->createMock(InputInterface::class);
        $this->output = $this->createMock(OutputInterface::class);
        // quiet to avoid the need to mock output helpers
        $this->output->method("getVerbosity")->willReturn(OutputInterface::VERBOSITY_QUIET);

        $this->command = new ImportCommand(
            $this->importer,
            $this->scraperCollection,
        );
    }

    public function testInvalidCityName(): void
    {
        $this->input
            ->method("getArgument")
            ->with($this->identicalTo("city_names"))
            ->willReturn([]);

        $this->scraperCollection
            ->method("filterByCityNames")
            ->willThrowException(new InvalidArgumentException());

        $this->expectException(SymfonyConsoleInvalidArgumentException::class);
        $this->command->run($this->input, $this->output);
    }

    public function testValidCityName(): void
    {
        $this->input
            ->method("getArgument")
            ->with($this->identicalTo("city_names"))
            ->willReturn([]);

        $this->scraperCollection
            ->method("filterByCityNames")
            ->willReturn([$this->createStub(CityScraper::class)]);

        $this->importer
            ->expects($this->once())
            ->method("import")
            ->with(
                $this->isInstanceOf(CityDTO::class),
            );

        $this->command->run($this->input, $this->output);
    }
}
