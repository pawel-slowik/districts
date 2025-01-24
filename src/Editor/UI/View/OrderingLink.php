<?php

declare(strict_types=1);

namespace Districts\Editor\UI\View;

use Psr\Http\Message\UriInterface;

readonly class OrderingLink
{
    private function __construct(
        public UriInterface $url,
        public bool $isOrderedAscending,
        public bool $isOrderedDescending,
    ) {
    }

    public static function createUnordered(UriInterface $url): self
    {
        return new self($url, false, false);
    }

    public static function createOrderedAscending(UriInterface $url): self
    {
        return new self($url, true, false);
    }

    public static function createOrderedDescending(UriInterface $url): self
    {
        return new self($url, false, true);
    }
}
