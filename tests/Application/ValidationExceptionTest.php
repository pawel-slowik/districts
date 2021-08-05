<?php

declare(strict_types=1);

namespace Districts\Test\Application;

use Districts\Application\ValidationException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Districts\Application\ValidationException
 */
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
