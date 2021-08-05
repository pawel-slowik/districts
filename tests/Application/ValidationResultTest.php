<?php

declare(strict_types=1);

namespace Districts\Test\Application;

use Districts\Application\ValidationResult;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Districts\Application\ValidationResult
 */
class ValidationResultTest extends TestCase
{
    public function testNewInstanceIsOk(): void
    {
        $result = new ValidationResult();

        $this->assertTrue($result->isOk());
    }

    public function testIsNotOkAfterAddingError(): void
    {
        $result = new ValidationResult();
        $result->addError("");

        $this->assertFalse($result->isOk());
    }

    public function testReturnsAllErrors(): void
    {
        $result = new ValidationResult();
        $result->addError("foo");
        $result->addError("bar");

        $this->assertEqualsCanonicalizing(["bar", "foo"], $result->getErrors());
    }
}
