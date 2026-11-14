<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Php70\Rector\MethodCall\ThisCallOnStaticMethodToStaticCallRector;
use Rector\Php80\Rector\Class_\ClassPropertyAssignToConstructorPromotionRector;
use Rector\Php80\Rector\Class_\StringableForToStringRector;
use Rector\ValueObject\PhpVersion;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/../../public',
        __DIR__ . '/../../src',
        __DIR__ . '/../../tests',
    ])
    ->withPhpVersion(PhpVersion::PHP_85)
    ->withPhpSets(php85: true)
    ->withSkip([
        // PHP 7.0
        ThisCallOnStaticMethodToStaticCallRector::class,

        // PHP 8.0
        ClassPropertyAssignToConstructorPromotionRector::class,
        StringableForToStringRector::class,
    ]);
