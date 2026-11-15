<?php

declare(strict_types=1);

namespace Districts\Editor\UI\View;

use InvalidArgumentException;
use Uri\WhatWg\InvalidUrlException;
use Uri\WhatWg\Url;

readonly class PageReference
{
    private function __construct(
        public ?string $url,
        public string $text,
        public bool $isCurrent,
        public bool $isPrevious,
        public bool $isNext,
    ) {
        if (!self::validateUrl($url)) {
            throw new InvalidArgumentException();
        }
    }

    public static function forPrevious(?string $url): self
    {
        return new self($url, "previous", false, true, false);
    }

    public static function forNext(?string $url): self
    {
        return new self($url, "next", false, false, true);
    }

    public static function forNumber(?string $url, int $pageNumber, bool $isCurrent): self
    {
        return new self($url, strval($pageNumber), $isCurrent, false, false);
    }

    private static function validateUrl(?string $url): bool
    {
        if ($url === null) {
            return true;
        }
        if ($url === "") {
            return false;
        }
        $baseUrl = new Url("http://base.url"); // allow relative URLs
        try {
            $parsedUrl = new Url($url, $baseUrl);
        } catch (InvalidUrlException) {
            return false;
        }
        return in_array($parsedUrl->getScheme(), ["http", "https"], true);
    }
}
