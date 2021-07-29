<?php

declare(strict_types=1);

namespace Districts\Test\Application;

use Districts\Application\Command\UpdateDistrictCommand;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Districts\Application\Command\UpdateDistrictCommand
 */
class UpdateDistrictCommandTest extends TestCase
{
    public function testGetters(): void
    {
        $command = new UpdateDistrictCommand(505, "new name", 60.6, 707);

        $this->assertSame(505, $command->getId());
        $this->assertSame("new name", $command->getName());
        $this->assertSame(60.6, $command->getArea());
        $this->assertSame(707, $command->getPopulation());
    }
}
