<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Symfony\Set\SymfonySetList;

return RectorConfig::configure()
                   ->withPaths([
                       __DIR__ . '/src',
                   ])
                   ->withPhpSets(php83: true)
                   ->withAttributesSets(
                       symfony: true,
                       doctrine: true,
                       sensiolabs: true
                   )
                   ->withSymfonyContainerXml(__DIR__ . '/var/cache/dev/App_KernelDevDebugContainer.xml')
                   ->withSets([
                       SymfonySetList::SYMFONY_54,
                       SymfonySetList::SYMFONY_60,
                       SymfonySetList::SYMFONY_61,
                       SymfonySetList::SYMFONY_62,
                       SymfonySetList::SYMFONY_63,
                       SymfonySetList::SYMFONY_64,
                       SymfonySetList::SYMFONY_70,
                       SymfonySetList::SYMFONY_71,
                       SymfonySetList::SYMFONY_CONSTRUCTOR_INJECTION,
                   ])
                   ->withPreparedSets(
                       typeDeclarations: true,
                       symfonyCodeQuality: true
                   )
;

