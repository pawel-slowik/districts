<?php

declare(strict_types=1);

namespace Districts\Test\UI\Web\Factory;

use Psr\Http\Message\ServerRequestInterface as Request;
use Districts\UI\Web\Factory\RemoveDistrictCommandFactory;
use Districts\Application\Command\RemoveDistrictCommand;
use Districts\Application\ValidationException;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * @covers \Districts\Application\Command\RemoveDistrictCommand
 * @covers \Districts\UI\Web\Factory\RemoveDistrictCommandFactory
 */
class RemoveDistrictCommandFactoryTest extends TestCase
{
    /**
     * @var MockObject|Request
     */
    private $request;

    /**
     * @var RemoveDistrictCommandFactory
     */
    private $commandFactory;

    protected function setUp(): void
    {
        $this->request = $this->createMock(Request::class);
        $this->commandFactory = new RemoveDistrictCommandFactory();
    }

    /**
     * @dataProvider unparseableRequestDataProvider
     */
    public function testUnparseableRemoveRequest(?object $parsedBody): void
    {
        $this->request->method("getParsedBody")->willReturn($parsedBody);
        $this->expectException(ValidationException::class);
        $this->commandFactory->fromRequest($this->request, ["id" => "1"]);
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
        $this->request->method("getParsedBody")->willReturn(["confirm" => ""]);
        $command = $this->commandFactory->fromRequest($this->request, ["id" => "1"]);
        $this->assertInstanceOf(RemoveDistrictCommand::class, $command);
        $this->assertSame(1, $command->getId());
        $this->assertTrue($command->isConfirmed());
    }

    public function testIncompleteRemoveRequest(): void
    {
        $this->request->method("getParsedBody")->willReturn([]);
        $this->expectException(ValidationException::class);
        $this->commandFactory->fromRequest($this->request, []);
    }
}
