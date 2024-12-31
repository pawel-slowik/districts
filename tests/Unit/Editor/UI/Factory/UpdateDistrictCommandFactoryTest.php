<?php

declare(strict_types=1);

namespace Districts\Test\Unit\Editor\UI\Factory;

use Districts\Editor\Application\Command\UpdateDistrictCommand;
use Districts\Editor\Application\Exception\ValidationException;
use Districts\Editor\UI\Factory\UpdateDistrictCommandFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface as Request;
use StdClass;

#[CoversClass(UpdateDistrictCommandFactory::class)]
class UpdateDistrictCommandFactoryTest extends TestCase
{
    /** @var MockObject&Request */
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
        $this->assertSame(1, $command->id);
        $this->assertSame("foo", $command->name);
        $this->assertSame(2.2, $command->area);
        $this->assertSame(3, $command->population);
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
        $this->assertSame("foo", $command->name);
    }

    /**
     * @param array<string, string> $requestData
     * @param array<string, string> $routeArgs
     */
    #[DataProvider('incompleteUpdateRequestDataProvider')]
    public function testIncompleteUpdateRequest(array $requestData, array $routeArgs): void
    {
        $this->request->method("getParsedBody")->willReturn($requestData);
        $this->expectException(ValidationException::class);
        $this->commandFactory->fromRequest($this->request, $routeArgs);
    }

    /**
     * @return array<string, array{0: array<string, string>, 1: array<string, string>}>
     */
    public static function incompleteUpdateRequestDataProvider(): array
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

    #[DataProvider('unparseableRequestDataProvider')]
    public function testUnparseableUpdateRequest(?object $parsedBody): void
    {
        $this->request->method("getParsedBody")->willReturn($parsedBody);
        $this->expectException(ValidationException::class);
        $this->commandFactory->fromRequest($this->request, ["id" => "1"]);
    }

    /**
     * @return array<array{0: null|object}>
     */
    public static function unparseableRequestDataProvider(): array
    {
        return [
            [null],
            [new StdClass()],
        ];
    }
}
