<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Sulu\Messenger\Infrastructure\Symfony\Messenger\FlushMiddleware\DoctrineFlushMiddleware;
use Sulu\Messenger\Infrastructure\Symfony\Messenger\UnpackExceptionMiddleware\UnpackExceptionMiddleware;

return function (ContainerConfigurator $configurator) {
    $services = $configurator->services();

    $services->set('sulu_messenger.doctrine_flush_middleware')
        ->class(DoctrineFlushMiddleware::class)
        ->args([service('doctrine.orm.entity_manager')]);

    $services->set('sulu_messenger.unpack_exception_middleware')
        ->class(UnpackExceptionMiddleware::class);
};
