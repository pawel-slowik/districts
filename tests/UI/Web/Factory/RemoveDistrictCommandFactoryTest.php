<?php

declare(strict_types=1);

namespace Districts\Test\UI\Web\Factory;

use Districts\Application\Command\RemoveDistrictCommand;
use Districts\Application\Exception\ValidationException;
use Districts\UI\Web\Factory\RemoveDistrictCommandFactory;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Districts\UI\Web\Factory\RemoveDistrictCommandFactory
 */
class RemoveDistrictCommandFactoryTest extends TestCase
{
    /**
     * @var RemoveDistrictCommandFactory
     */
    private $commandFactory;

    protected function setUp(): void
    {
        $this->commandFactory = new RemoveDistrictCommandFactory();
    }

    public function testValidRemoveRequest(): void
    {
        $command = $this->commandFactory->fromRoute(["id" => "1"]);
        $this->assertInstanceOf(RemoveDistrictCommand::class, $command);
        $this->assertSame(1, $command->getId());
    }

    public function testIncompleteRemoveRequest(): void
    {
        $this->expectException(ValidationException::class);
        $this->commandFactory->fromRoute([]);
    }
}
