<?php

declare(strict_types=1);

namespace Districts\Test\UI\Web\Factory;

use Districts\Application\Command\RemoveDistrictCommand;
use Districts\Application\Exception\ValidationException;
use Districts\UI\Web\Factory\RemoveDistrictCommandFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
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

    public function testValidRemoveRequest(): void
    {
        $command = $this->commandFactory->fromRequest($this->request, ["id" => "1"]);
        $this->assertInstanceOf(RemoveDistrictCommand::class, $command);
        $this->assertSame(1, $command->getId());
    }

    public function testIncompleteRemoveRequest(): void
    {
        $this->expectException(ValidationException::class);
        $this->commandFactory->fromRequest($this->request, []);
    }
}
