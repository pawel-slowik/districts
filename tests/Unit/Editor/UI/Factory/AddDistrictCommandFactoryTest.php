<?php

declare(strict_types=1);

namespace Districts\Test\Unit\Editor\UI\Factory;

use Districts\Editor\Application\Exception\ValidationException;
use Districts\Editor\UI\Factory\AddDistrictCommandFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface as Request;
use StdClass;

#[CoversClass(AddDistrictCommandFactory::class)]
class AddDistrictCommandFactoryTest extends TestCase
{
    /** @var MockObject&Request */
    private Request $request;

    private AddDistrictCommandFactory $commandFactory;

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
        $this->assertSame(1, $command->cityId);
        $this->assertSame("foo", $command->name);
        $this->assertSame(2.2, $command->area);
        $this->assertSame(3, $command->population);
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
        $this->assertSame("foo", $command->name);
    }

    /**
     * @param array<string, string> $requestData
     */
    #[DataProvider('incompleteAddRequestDataProvider')]
    public function testIncompleteAddRequest(array $requestData): void
    {
        $this->request->method("getParsedBody")->willReturn($requestData);
        $this->expectException(ValidationException::class);
        $this->commandFactory->fromRequest($this->request);
    }

    /**
     * @return array<string, array{0: array<string, string>}>
     */
    public static function incompleteAddRequestDataProvider(): array
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

    #[DataProvider('unparseableRequestDataProvider')]
    public function testUnparseableAddRequest(?object $parsedBody): void
    {
        $this->request->method("getParsedBody")->willReturn($parsedBody);
        $this->expectException(ValidationException::class);
        $this->commandFactory->fromRequest($this->request);
    }

    /**
     * @return array<array{0: ?object}>
     */
    public static function unparseableRequestDataProvider(): array
    {
        return [
            [null],
            [new StdClass()],
        ];
    }
}
