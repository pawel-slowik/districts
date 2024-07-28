<?php

declare(strict_types=1);

namespace Districts\Test\Unit\Editor\UI\ErrorHandler;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

class BaseTestCase extends TestCase
{
    protected ServerRequestInterface $requestMock;

    protected Throwable $exceptionMock;

    protected function setUp(): void
    {
        $this->requestMock = $this->createMock(ServerRequestInterface::class);
        $this->exceptionMock = $this->createMock(Throwable::class);
    }
}
