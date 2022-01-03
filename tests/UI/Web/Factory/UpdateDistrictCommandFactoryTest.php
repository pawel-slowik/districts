<?php

declare(strict_types=1);

namespace Districts\Test\UI\Web\Factory;

use Districts\Application\Command\UpdateDistrictCommand;
use Districts\Application\Exception\ValidationException;
use Districts\UI\Web\Factory\UpdateDistrictCommandFactory;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface as Request;
use StdClass;

/**
 * @covers \Districts\UI\Web\Factory\UpdateDistrictCommandFactory
 */
class UpdateDistrictCommandFactoryTest extends TestCase
{
    private Request $request;

    private UpdateDistrictCommandFactory $commandFactory;

    protected function setUp(): void
    {
        $this->request = $this->createMock(Request::class);
        $this->commandFactory = new UpdateDistrictCommandFactory();
    }

    public function testValidUpdateRequest(): void
    {
        $this->request->method("getParsedBody")->willReturn(
            [
                "name" => "foo",
                "area" => "2.2",
                "population" => "3",
            ]
        );
        $command = $this->commandFactory->fromRequest($this->request, ["id" => "1"]);
        $this->assertInstanceOf(UpdateDistrictCommand::class, $command);
        $this->assertSame(1, $command->getId());
        $this->assertSame("foo", $command->getName());
        $this->assertSame(2.2, $command->getArea());
        $this->assertSame(3, $command->getPopulation());
    }

    public function testTrimsNameInUpdateRequest(): void
    {
        $this->request->method("getParsedBody")->willReturn(
            [
                "name" => " foo ",
                "area" => "2.2",
                "population" => "3",
            ]
        );
        $command = $this->commandFactory->fromRequest($this->request, ["id" => "1"]);
        $this->assertSame("foo", $command->getName());
    }

    /**
     * @dataProvider incompleteUpdateRequestDataProvider
     */
    public function testIncompleteUpdateRequest(array $requestData, array $routeArgs): void
    {
        $this->request->method("getParsedBody")->willReturn($requestData);
        $this->expectException(ValidationException::class);
        $this->commandFactory->fromRequest($this->request, $routeArgs);
    }

    public function incompleteUpdateRequestDataProvider(): array
    {
        return [
            "missing_id" => [
                [
                    "name" => "foo",
                    "area" => "2.2",
                    "population" => "3",
                ],
                [
                ],
            ],
            "missing_name" => [
                [
                    "area" => "2.2",
                    "population" => "3",
                ],
                [
                    "id" => "1",
                ],
            ],
            "missing_area" => [
                [
                    "name" => "foo",
                    "population" => "3",
                ],
                [
                    "id" => "1",
                ],
            ],
            "missing_population" => [
                [
                    "name" => "foo",
                    "area" => "2.2",
                ],
                [
                    "id" => "1",
                ],
            ],
        ];
    }

    /**
     * @dataProvider unparseableRequestDataProvider
     */
    public function testUnparseableUpdateRequest(?object $parsedBody): void
    {
        $this->request->method("getParsedBody")->willReturn($parsedBody);
        $this->expectException(ValidationException::class);
        $this->commandFactory->fromRequest($this->request, ["id" => "1"]);
    }

    public function unparseableRequestDataProvider(): array
    {
        return [
            [null],
            [new StdClass()],
        ];
    }
}
