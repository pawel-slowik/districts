<?php

declare(strict_types=1);

namespace Districts\Test\Unit\Editor\Application;

use Districts\Editor\Application\Exception\ValidationException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ValidationException::class)]
class ValidationExceptionTest extends TestCase
{
    public function testNewInstanceHasNoErrors(): void
    {
        $exception = new ValidationException();

        $this->assertCount(0, $exception->getErrors());
    }

    public function testSettingErrors(): void
    {
        $exception = new ValidationException();
        $exception = $exception->withErrors(["foo"]);

        $this->assertSame(["foo"], $exception->getErrors());
    }
}
