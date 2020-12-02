<?php

declare(strict_types=1);

namespace Test\UI\Web;

use Psr\Http\Message\ServerRequestInterface as Request;
use UI\Web\RequestParser;
use Application\Command\AddDistrictCommand;
use Application\Command\RemoveDistrictCommand;
use Application\Command\UpdateDistrictCommand;
use Service\ValidationException;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * @covers \Application\Command\AddDistrictCommand
 * @covers \Application\Command\RemoveDistrictCommand
 * @covers \Application\Command\UpdateDistrictCommand
 * @covers \UI\Web\RequestParser
 */
class RequestParserTest extends TestCase
{
    /**
     * @var MockObject|Request
     */
    private $request;

    /**
     * @var RequestParser
     */
    private $requestParser;

    protected function setUp(): void
    {
        $this->request = $this->createMock(Request::class);
        $this->requestParser = new RequestParser();
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
        $command = $this->requestParser->parseAdd($this->request);
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
        $command = $this->requestParser->parseAdd($this->request);
        $this->assertSame("foo", $command->getName());
    }

    /**
     * @dataProvider incompleteAddRequestDataProvider
     */
    public function testIncompleteAddRequest(array $requestData): void
    {
        $this->request->method("getParsedBody")->willReturn($requestData);
        $this->expectException(ValidationException::class);
        $this->requestParser->parseAdd($this->request);
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

    public function testValidUpdateRequest(): void
    {
        $this->request->method("getParsedBody")->willReturn(
            [
                "name" => "foo",
                "area" => "2.2",
                "population" => "3",
            ]
        );
        $command = $this->requestParser->parseUpdate($this->request, ["id" => "1"]);
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
        $command = $this->requestParser->parseUpdate($this->request, ["id" => "1"]);
        $this->assertSame("foo", $command->getName());
    }

    /**
     * @dataProvider incompleteUpdateRequestDataProvider
     */
    public function testIncompleteUpdateRequest(array $requestData, array $routeArgs): void
    {
        $this->request->method("getParsedBody")->willReturn($requestData);
        $this->expectException(ValidationException::class);
        $this->requestParser->parseUpdate($this->request, $routeArgs);
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
    public function testUnparseableAddRequest(?object $parsedBody): void
    {
        $this->request->method("getParsedBody")->willReturn($parsedBody);
        $this->expectException(ValidationException::class);
        $this->requestParser->parseAdd($this->request);
    }

    /**
     * @dataProvider unparseableRequestDataProvider
     */
    public function testUnparseableUpdateRequest(?object $parsedBody): void
    {
        $this->request->method("getParsedBody")->willReturn($parsedBody);
        $this->expectException(ValidationException::class);
        $this->requestParser->parseUpdate($this->request, ["id" => "1"]);
    }

    public function unparseableRequestDataProvider(): array
    {
        return [
            [null],
            [new \StdClass()],
        ];
    }

    public function testValidRemoveRequest(): void
    {
        $this->request->method("getParsedBody")->willReturn([]);
        $command = $this->requestParser->parseRemove($this->request, ["id" => "1"]);
        $this->assertInstanceOf(RemoveDistrictCommand::class, $command);
        $this->assertSame(1, $command->getId());
    }

    public function testIncompleteRemoveRequest(): void
    {
        $this->request->method("getParsedBody")->willReturn([]);
        $this->expectException(ValidationException::class);
        $this->requestParser->parseRemove($this->request, []);
    }
}
