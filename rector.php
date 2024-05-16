<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Doctrine\Set\DoctrineSetList;
use Rector\PHPUnit\Set\PHPUnitLevelSetList;
use Rector\PHPUnit\Set\PHPUnitSetList;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;
use Rector\Symfony\Set\SymfonyLevelSetList;
use Rector\Symfony\Set\SymfonySetList;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->paths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ]);

    $rectorConfig->phpstanConfigs([
        __DIR__ . '/phpstan.dist.neon',
        // rector does not load phpstan extension automatically so require them manually here:
        __DIR__ . '/vendor/phpstan/phpstan-doctrine/extension.neon',
        __DIR__ . '/vendor/phpstan/phpstan-symfony/extension.neon',
    ]);

    $rectorConfig->skip([
        __DIR__ . '/tests/Application/var',
        __DIR__ . '/tests/Application/config',
    ]);

    // basic rules
    $rectorConfig->importNames();
    $rectorConfig->importShortClasses(false);

    $rectorConfig->sets([
        SetList::CODE_QUALITY,
        // LevelSetList::UP_TO_PHP_80,
    ]);

    // symfony rules
    $rectorConfig->symfonyContainerPhp(__DIR__ . '/tests/Application/var/cache/dev/Sulu_Messenger_Tests_Application_KernelDevDebugContainer.xml');

    $rectorConfig->sets([
        SymfonySetList::SYMFONY_CODE_QUALITY,
        SymfonySetList::SYMFONY_CONSTRUCTOR_INJECTION,
        // SymfonyLevelSetList::UP_TO_SYMFONY_54,
    ]);

    // doctrine rules
    $rectorConfig->sets([
        DoctrineSetList::DOCTRINE_CODE_QUALITY,
    ]);

    // phpunit rules
    $rectorConfig->sets([
        // PHPUnitLevelSetList::UP_TO_PHPUNIT_90,
        // PHPUnitSetList::PHPUNIT_91,
    ]);
};
