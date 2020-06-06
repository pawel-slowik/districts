<?php

declare(strict_types=1);

namespace Test\ErrorHandlers;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

class BaseTestCase extends TestCase
{
    protected $requestMock;

    protected $exceptionMock;

    protected function setUp(): void
    {
        $this->requestMock = $this->createMock(ServerRequestInterface::class);
        $this->exceptionMock = $this->createMock(\Throwable::class);
    }
}
