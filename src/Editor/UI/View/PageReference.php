<?php

declare(strict_types=1);

namespace Districts\Editor\UI\View;

use InvalidArgumentException;
use Laminas\Uri\Exception\InvalidArgumentException as UriException;
use Laminas\Uri\Uri;

class PageReference
{
    public function __construct(
        private ?string $url,
        private string $text,
        private bool $isCurrent,
        private bool $isPrevious,
        private bool $isNext,
    ) {
        if (!self::validate($url, $text, $isCurrent, $isPrevious, $isNext)) {
            throw new InvalidArgumentException();
        }
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function isCurrent(): bool
    {
        return $this->isCurrent;
    }

    public function isPrevious(): bool
    {
        return $this->isPrevious;
    }

    public function isNext(): bool
    {
        return $this->isNext;
    }

    private static function validate(?string $url, string $text, bool $isCurrent, bool $isPrevious, bool $isNext): bool
    {
        return
            self::validateUrl($url)
            && self::validateText($text)
            && self::validateFlags($isCurrent, $isPrevious, $isNext);
    }

    private static function validateUrl(?string $url): bool
    {
        if (is_null($url)) {
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
            && is_null($uri->getScheme())
            && is_null($uri->getUserInfo())
            && is_null($uri->getHost())
            && is_null($uri->getPort());
    }

    private static function validateText(string $text): bool
    {
        return $text !== "";
    }

    private static function validateFlags(bool $isCurrent, bool $isPrevious, bool $isNext): bool
    {
        return
            (!$isCurrent && !$isPrevious && !$isNext)
            || (!$isCurrent && !$isPrevious && $isNext)
            || (!$isCurrent && $isPrevious && !$isNext)
            || ($isCurrent && !$isPrevious && !$isNext);
    }
}
