<?php

declare(strict_types=1);

namespace Districts\Test\DomainModel\Scraper;

use Districts\DomainModel\Scraper\HtmlFetcher;
use PHPUnit\Framework\MockObject\MockObject;

class HtmlFetcherMockBuilder
{
    public static function buildFromUrlFilenameMap(
        MockObject $mock,
        array $urlFilenameMap
    ): HtmlFetcher {
        $mockMap = array_map(
            function ($url, $filename) {
                return [$url, file_get_contents($filename)];
            },
            array_keys($urlFilenameMap),
            $urlFilenameMap,
        );
        $mock->method("fetchHtml")->willReturnMap($mockMap);
        return $mock;
    }
}
