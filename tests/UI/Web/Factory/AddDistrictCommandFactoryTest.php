<?php

declare(strict_types=1);

namespace Districts\Test\UI\Web\Factory;

use Districts\Application\Command\AddDistrictCommand;
use Districts\Application\ValidationException;
use Districts\UI\Web\Factory\AddDistrictCommandFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface as Request;
use StdClass;

/**
 * @covers \Districts\Application\Command\AddDistrictCommand
 * @covers \Districts\UI\Web\Factory\AddDistrictCommandFactory
 */
class AddDistrictCommandFactoryTest extends TestCase
{
    /**
     * @var MockObject|Request
     */
    private $request;

    /**
     * @var AddDistrictCommandFactory
     */
    private $commandFactory;

    protected function setUp(): void
    {
        $this->request = $this->createMock(Request::class);
        $this->commandFactory = new AddDistrictCommandFactory();
    }

    public function testValidAddRequest(): void
    {
        $this->request->method("getParsedBody")->willReturn(
            [
                "city" => "1",
                "name" => "foo",
                "area" => "2.2",
                "population" => "3",
            ]
        );
        $command = $this->commandFactory->fromRequest($this->request);
        $this->assertInstanceOf(AddDistrictCommand::class, $command);
        $this->assertSame(1, $command->getCityId());
        $this->assertSame("foo", $command->getName());
        $this->assertSame(2.2, $command->getArea());
        $this->assertSame(3, $command->getPopulation());
    }

    public function testTrimsNameInAddRequest(): void
    {
        $this->request->method("getParsedBody")->willReturn(
            [
                "city" => "1",
                "name" => " foo ",
                "area" => "2.2",
                "population" => "3",
            ]
        );
        $command = $this->commandFactory->fromRequest($this->request);
        $this->assertSame("foo", $command->getName());
    }

    /**
     * @dataProvider incompleteAddRequestDataProvider
     */
    public function testIncompleteAddRequest(array $requestData): void
    {
        $this->request->method("getParsedBody")->willReturn($requestData);
        $this->expectException(ValidationException::class);
        $this->commandFactory->fromRequest($this->request);
    }

    public function incompleteAddRequestDataProvider(): array
    {
        return [
            "missing_city" => [
                [
                    "name" => "foo",
                    "area" => "2.2",
                    "population" => "3",
                ],
            ],
            "missing_name" => [
                [
                    "city" => "1",
                    "area" => "2.2",
                    "population" => "3",
                ],
            ],
            "missing_area" => [
                [
                    "city" => "1",
                    "name" => "foo",
                    "population" => "3",
                ],
            ],
            "missing_population" => [
                [
                    "city" => "1",
                    "name" => "foo",
                    "area" => "2.2",
                ],
            ],
        ];
    }

    /**
     * @dataProvider unparseableRequestDataProvider
     */
    public function testUnparseableAddRequest(?object $parsedBody): void
    {
        $this->request->method("getParsedBody")->willReturn($parsedBody);
        $this->expectException(ValidationException::class);
        $this->commandFactory->fromRequest($this->request);
    }

    public function unparseableRequestDataProvider(): array
    {
        return [
            [null],
            [new StdClass()],
        ];
    }
}
