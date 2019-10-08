<?php

declare(strict_types=1);

namespace Scraper;

use PHPUnit\Framework\TestCase;

class ScraperTestBase extends TestCase
{
    protected function loadTestFile($filename): string
    {
        return file_get_contents(__DIR__ . "/data/" . $filename);
    }
}
