<?php

declare(strict_types=1);

namespace Districts\Test\Unit\Editor\UI\Factory;

use Districts\Editor\UI\Factory\AddDistrictCommandFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface as Request;

#[CoversClass(AddDistrictCommandFactory::class)]
final class AddDistrictCommandFactoryTest extends TestCase
{
    private Request&Stub $request;

    private AddDistrictCommandFactory $commandFactory;

    protected function setUp(): void
    {
        $this->request = $this->createStub(Request::class);
        $this->commandFactory = new AddDistrictCommandFactory();
    }

    public function testValidAddRequest(): void
    {
        $this->request->method("getParsedBody")->willReturn(
            [
                "city" => "1",
                "name" => "foo",
                "area" => "2.2",
                "population" => "3",
            ]
        );
        $command = $this->commandFactory->fromRequest($this->request);
        $this->assertSame(1, $command->cityId);
        $this->assertSame("foo", $command->name);
        $this->assertSame(2.2, $command->area);
        $this->assertSame(3, $command->population);
    }

    public function testTrimsNameInAddRequest(): void
    {
        $this->request->method("getParsedBody")->willReturn(
            [
                "city" => "1",
                "name" => " foo ",
                "area" => "2.2",
                "population" => "3",
            ]
        );
        $command = $this->commandFactory->fromRequest($this->request);
        $this->assertSame("foo", $command->name);
    }
}
