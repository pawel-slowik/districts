<?php

declare(strict_types=1);

namespace Test\Service;

use Service\DistrictFilter;

use PHPUnit\Framework\TestCase;

/**
 * @covers \Service\DistrictFilter
 */
class DistrictFilterTest extends TestCase
{
    public function testGetters(): void
    {
        $filter = new DistrictFilter(DistrictFilter::TYPE_CITY, "test");
        $this->assertSame(DistrictFilter::TYPE_CITY, $filter->getType());
        $this->assertSame("test", $filter->getValue());
    }
}
