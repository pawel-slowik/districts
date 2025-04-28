<?php

declare(strict_types=1);

namespace Districts\Test\Unit\Editor\UI\Factory;

use Districts\Editor\UI\Factory\RemoveDistrictCommandFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface as Request;

#[CoversClass(RemoveDistrictCommandFactory::class)]
final class RemoveDistrictCommandFactoryTest extends TestCase
{
    private RemoveDistrictCommandFactory $commandFactory;

    protected function setUp(): void
    {
        $this->commandFactory = new RemoveDistrictCommandFactory();
    }

    public function testValidRemoveRequest(): void
    {
        $request = $this->createStub(Request::class);
        $command = $this->commandFactory->fromRoute(["id" => "1"], $request);
        $this->assertSame(1, $command->id);
    }
}
