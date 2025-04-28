<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Php70\Rector\MethodCall\ThisCallOnStaticMethodToStaticCallRector;
use Rector\Php80\Rector\Class_\ClassPropertyAssignToConstructorPromotionRector;
use Rector\Php80\Rector\Class_\StringableForToStringRector;
use Rector\Php81\Rector\FuncCall\NullToStrictStringFuncCallArgRector;
use Rector\Php81\Rector\Property\ReadOnlyPropertyRector;
use Rector\Php82\Rector\Class_\ReadOnlyClassRector;
use Rector\Php83\Rector\ClassConst\AddTypeToConstRector;
use Rector\Php83\Rector\ClassMethod\AddOverrideAttributeToOverriddenMethodsRector;
use Rector\ValueObject\PhpVersion;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/dependencies',
        __DIR__ . '/public',
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])
    ->withPhpVersion(PhpVersion::PHP_83)
    ->withPhpSets(php83: true)
    ->withSkip([
        // PHP 7.0
        ThisCallOnStaticMethodToStaticCallRector::class,

        // PHP 8.0
        ClassPropertyAssignToConstructorPromotionRector::class,
        StringableForToStringRector::class,

        // PHP 8.1
        NullToStrictStringFuncCallArgRector::class,
        ReadOnlyPropertyRector::class,

        // PHP 8.2
        ReadOnlyClassRector::class,

        // PHP 8.3
        AddOverrideAttributeToOverriddenMethodsRector::class,
        AddTypeToConstRector::class,
    ]);
