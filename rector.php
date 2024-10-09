<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Php73\Rector\String_\SensitiveHereNowDocRector;
use Rector\Php80\Rector\Catch_\RemoveUnusedVariableInCatchRector;
use Rector\Php80\Rector\Class_\ClassPropertyAssignToConstructorPromotionRector;
use Rector\Php80\Rector\FunctionLike\MixedTypeRector;
use Rector\Php81\Rector\FuncCall\NullToStrictStringFuncCallArgRector;

return RectorConfig::configure()
    // Set the correct paths to all the PHP files in your project
    ->withPaths([
        __DIR__ . '/demo',
        __DIR__ . '/po',
        __DIR__ . '/Swat',
        __DIR__ . '/SwatDB',
        __DIR__ . '/SwatI18N',
    ])
    // Choose the correct PHP version for your project
    ->withPhpSets(php82: true)
    ->withRules([
        // any additional rules can be added here
        // see https://getrector.com/find-rule
    ])
    ->withSkip([
        // any rules that are part of the PHP set but that you want
        // to skip can be listed here.  The following are common ones
        // that introduce a lot of changes to legacy code bases.
        // For new projects, you should probably not skip anything.
        ClassPropertyAssignToConstructorPromotionRector::class,
        MixedTypeRector::class,
        NullToStrictStringFuncCallArgRector::class,
        RemoveUnusedVariableInCatchRector::class,
        SensitiveHereNowDocRector::class,
    ])
    // See https://getrector.com/documentation/integration-to-new-project
    // for other configuration settings
    ->withTypeCoverageLevel(1)
    ->withDeadCodeLevel(0);
