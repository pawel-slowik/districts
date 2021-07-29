<?php

declare(strict_types=1);

namespace Districts\Test\Application;

use Districts\Application\Query\GetDistrictQuery;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Districts\Application\Query\GetDistrictQuery
 */
class GetDistrictQueryTest extends TestCase
{
    public function testGetters(): void
    {
        $query = new GetDistrictQuery(55);

        $this->assertSame(55, $query->getId());
    }
}
