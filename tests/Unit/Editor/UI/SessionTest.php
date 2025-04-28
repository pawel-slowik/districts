<?php

declare(strict_types=1);

namespace Districts\Test\Unit\Editor\UI;

use Districts\Editor\UI\Session;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use SlimSession\Helper;

#[CoversClass(Session::class)]
final class SessionTest extends TestCase
{
    public function testSet(): void
    {
        $helper = $this->createMock(Helper::class);
        $session = new Session($helper);

        $helper
            ->expects($this->once())
            ->method("set")
            ->with("keY", "vaLuE");

        $session->set("keY", "vaLuE");
    }

    public function testDelete(): void
    {
        $helper = $this->createMock(Helper::class);
        $session = new Session($helper);

        $helper
            ->expects($this->once())
            ->method("delete")
            ->with("__key_to_DELETE");

        $session->delete("__key_to_DELETE");
    }

    public function testGetAndDeleteBothGetsAndDeletes(): void
    {
        $helper = $this->createMock(Helper::class);
        $session = new Session($helper);

        $helper
            ->expects($this->once())
            ->method("get")
            ->with("flash.message");
        $helper
            ->expects($this->once())
            ->method("delete")
            ->with("flash.message");

        $session->getAndDelete("flash.message");
    }

    public function testGetAndDeleteGetsBeforeDeleting(): void
    {
        $helper = $this->createStub(Helper::class);
        $session = new Session($helper);

        $callOrder = [];
        $helper
            ->method("get")
            ->willReturnCallback(
                // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
                static function (string $key, mixed $value) use (&$callOrder): mixed {
                    $callOrder[] = "get";
                    return "";
                }
            );
        $helper
            ->method("delete")
            ->willReturnCallback(
                // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
                static function (string $key) use (&$callOrder, &$helper): Helper {
                    $callOrder[] = "delete";
                    return $helper;
                }
            );

        $session->getAndDelete("flash.message");

        $this->assertSame(["get", "delete"], $callOrder);
    }
}
