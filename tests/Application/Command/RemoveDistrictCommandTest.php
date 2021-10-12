<?php

declare(strict_types=1);

namespace Districts\Test\Application;

use Districts\Application\Command\RemoveDistrictCommand;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Districts\Application\Command\RemoveDistrictCommand
 */
class RemoveDistrictCommandTest extends TestCase
{
    public function testGetters(): void
    {
        $command = new RemoveDistrictCommand(404);

        $this->assertSame(404, $command->getId());
    }
}
