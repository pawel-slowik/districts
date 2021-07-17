<?php

declare(strict_types=1);

namespace Districts\Test\UI\Web\ErrorHandler;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

class BaseTestCase extends TestCase
{
    /**
     * @var MockObject|ServerRequestInterface
     */
    protected $requestMock;

    /**
     * @var MockObject|Throwable
     */
    protected $exceptionMock;

    protected function setUp(): void
    {
        $this->requestMock = $this->createMock(ServerRequestInterface::class);
        $this->exceptionMock = $this->createMock(Throwable::class);
    }
}
