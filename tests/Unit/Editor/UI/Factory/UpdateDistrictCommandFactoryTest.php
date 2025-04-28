<?php

declare(strict_types=1);

namespace Districts\Test\Unit\Editor\UI\Factory;

use Districts\Editor\UI\Factory\UpdateDistrictCommandFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface as Request;

#[CoversClass(UpdateDistrictCommandFactory::class)]
final class UpdateDistrictCommandFactoryTest extends TestCase
{
    private Request&MockObject $request;

    private UpdateDistrictCommandFactory $commandFactory;

    protected function setUp(): void
    {
        $this->request = $this->createMock(Request::class);
        $this->commandFactory = new UpdateDistrictCommandFactory();
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
        $command = $this->commandFactory->fromRequestAndRoute($this->request, ["id" => "1"]);
        $this->assertSame(1, $command->id);
        $this->assertSame("foo", $command->name);
        $this->assertSame(2.2, $command->area);
        $this->assertSame(3, $command->population);
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
        $command = $this->commandFactory->fromRequestAndRoute($this->request, ["id" => "1"]);
        $this->assertSame("foo", $command->name);
    }
}
