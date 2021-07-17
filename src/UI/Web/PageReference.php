<?php

declare(strict_types=1);

namespace Districts\UI\Web;

use InvalidArgumentException;

class PageReference
{
    private $url;

    private $text;

    private $isCurrent;

    private $isPrevious;

    private $isNext;

    public function __construct(?string $url, string $text, bool $isCurrent, bool $isPrevious, bool $isNext)
    {
        if (!self::validate($url, $text, $isCurrent, $isPrevious, $isNext)) {
            throw new InvalidArgumentException();
        }
        $this->url = $url;
        $this->text = $text;
        $this->isCurrent = $isCurrent;
        $this->isPrevious = $isPrevious;
        $this->isNext = $isNext;
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
        return is_null($url) || self::validateAbsoluteUrl($url) || self::validateRelativeUrl($url);
    }

    private static function validateAbsoluteUrl(string $url): bool
    {
        $checkedUrl = filter_var($url, FILTER_VALIDATE_URL, FILTER_FLAG_SCHEME_REQUIRED | FILTER_FLAG_HOST_REQUIRED);
        if (is_string($checkedUrl)) {
            $scheme = parse_url($checkedUrl, PHP_URL_SCHEME);
            if (is_string($scheme)) {
                if (in_array(strtolower($scheme), ["http", "https"], true)) {
                    return true;
                }
            }
        }
        return false;
    }

    // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
    private static function validateRelativeUrl(string $url): bool
    {
        // TODO: add support for relative URLs
        return false;
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
