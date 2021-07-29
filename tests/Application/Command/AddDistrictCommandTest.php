<?php

declare(strict_types=1);

namespace Districts\Test\Application;

use Districts\Application\Command\AddDistrictCommand;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Districts\Application\Command\AddDistrictCommand
 */
class AddDistrictCommandTest extends TestCase
{
    public function testGetters(): void
    {
        $command = new AddDistrictCommand(101, "name", 20.2, 303);

        $this->assertSame(101, $command->getCityId());
        $this->assertSame("name", $command->getName());
        $this->assertSame(20.2, $command->getArea());
        $this->assertSame(303, $command->getPopulation());
    }
}
