<?php

declare(strict_types=1);

namespace Districts\Editor\UI\View;

use InvalidArgumentException;
use Laminas\Uri\Exception\InvalidArgumentException as UriException;
use Laminas\Uri\Uri;

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
        try {
            $uri = new Uri($url);
        } catch (UriException) {
            return false;
        }
        return self::validateAbsoluteUri($uri) || self::validateRelativeUri($uri);
    }

    private static function validateAbsoluteUri(Uri $uri): bool
    {
        return $uri->isValid() && in_array($uri->getScheme(), ["http", "https"], true);
    }

    private static function validateRelativeUri(Uri $uri): bool
    {
        return $uri->isValidRelative()
            && ($uri->getScheme() === null)
            && ($uri->getUserInfo() === null)
            && ($uri->getHost() === null)
            && ($uri->getPort() === null);
    }
}
