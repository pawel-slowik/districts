<?php

declare(strict_types=1);

namespace Districts\Test\Unit\Editor\UI\Factory;

use Districts\Editor\Application\Exception\ValidationException;
use Districts\Editor\UI\Factory\RemoveDistrictCommandFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(RemoveDistrictCommandFactory::class)]
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
        $this->assertSame(1, $command->id);
    }

    public function testIncompleteRemoveRequest(): void
    {
        $this->expectException(ValidationException::class);
        $this->commandFactory->fromRoute([]);
    }
}
