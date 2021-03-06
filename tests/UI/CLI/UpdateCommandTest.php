<?php

declare(strict_types=1);

namespace Districts\Test\UI\CLI;

use Districts\UI\CLI\UpdateCommand;
use Districts\Application\Importer;
use Districts\Application\ProgressReporter;
use Districts\DomainModel\Scraper\CityDTO;
use Districts\DomainModel\Scraper\HtmlFetcher;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Exception\InvalidArgumentException;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Districts\UI\CLI\UpdateCommand
 */
class UpdateCommandTest extends TestCase
{
    /**
     * @var Importer|MockObject
     */
    private $importer;

    /**
     * @var HtmlFetcher|MockObject
     */
    private $fetcher;

    /**
     * @var UpdateCommand
     */
    private $command;

    /**
     * @var InputInterface|MockObject
     */
    private $input;

    /**
     * @var MockObject|OutputInterface
     */
    private $output;

    protected function setUp(): void
    {
        $this->importer = $this->createMock(Importer::class);
        $this->fetcher = $this->createMock(HtmlFetcher::class);
        $this->command = new UpdateCommand($this->importer, $this->fetcher);
        $this->input = $this->createMock(InputInterface::class);
        $this->output = $this->createMock(OutputInterface::class);
        // quiet to avoid the need to mock output helpers
        $this->output->method("getVerbosity")->willReturn(OutputInterface::VERBOSITY_QUIET);
    }

    public function testInvalidCityName(): void
    {
        $this->input
            ->method("getArgument")
            ->with($this->equalTo("city_names"))
            ->willReturn(["Foo"]);
        $this->expectException(InvalidArgumentException::class);
        $this->command->run($this->input, $this->output);
    }

    public function testValidCityName(): void
    {
        $this->input
            ->method("getArgument")
            ->with($this->equalTo("city_names"))
            ->willReturn(["Gdańsk"]);

        $listHtml = file_get_contents(__DIR__ . "/../../DomainModel/Scraper/Gdansk/dzielnice.html");
        $entryHtml = file_get_contents(__DIR__ . "/../../DomainModel/Scraper/Gdansk/dzielnice_mapa_alert.php?id=16");
        $fetcherReturnValues = [$listHtml] + array_fill(1, 35, $entryHtml);
        $this->fetcher
            ->expects(
                $this->exactly(count($fetcherReturnValues))
            )
            ->method("fetchHtml")
            ->will(
                $this->onConsecutiveCalls(...$fetcherReturnValues)
            );

        $this->importer
            ->expects($this->once())
            ->method("import")
            ->with(
                $this->isInstanceOf(CityDTO::class),
                $this->isInstanceOf(ProgressReporter::class),
            )
            ->will(
                $this->returnCallback(
                    // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
                    function (CityDTO $cityDTO, ProgressReporter $progressReporter): void {
                        $this->assertSame("Gdańsk", $cityDTO->getName());
                        // start the generator
                        iterator_to_array($cityDTO->listDistricts());
                    }
                )
            );

        $this->command->run($this->input, $this->output);
    }
}
