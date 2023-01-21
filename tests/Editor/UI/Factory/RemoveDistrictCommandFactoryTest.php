<?php

declare(strict_types=1);

namespace Districts\Test\Editor\UI\Factory;

use Districts\Application\Command\RemoveDistrictCommand;
use Districts\Application\Exception\ValidationException;
use Districts\Editor\UI\Factory\RemoveDistrictCommandFactory;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Districts\Editor\UI\Factory\RemoveDistrictCommandFactory
 */
class RemoveDistrictCommandFactoryTest extends TestCase
{
    private RemoveDistrictCommandFactory $commandFactory;

    protected function setUp(): void
    {
        $this->commandFactory = new RemoveDistrictCommandFactory();
    }

    public function testValidRemoveRequest(): void
    {
        $command = $this->commandFactory->fromRoute(["id" => "1"]);
        $this->assertInstanceOf(RemoveDistrictCommand::class, $command);
        $this->assertSame(1, $command->id);
    }

    public function testIncompleteRemoveRequest(): void
    {
        $this->expectException(ValidationException::class);
        $this->commandFactory->fromRoute([]);
    }
}
