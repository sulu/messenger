<?php

declare(strict_types=1);

namespace Sulu\Messenger\Infrastructure\Symfony\Messenger\FlushMiddleware;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;
use Symfony\Contracts\Service\ServiceSubscriberInterface;

class DoctrineFlushMiddleware implements MiddlewareInterface, ServiceSubscriberInterface
{
    public function __construct(private ContainerInterface $container)
    {
    }

    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        $envelope = $stack->next()->handle($envelope, $stack);

        // flush unit-of-work to the database after the root message was handled successfully
        if (!empty($envelope->all(EnableFlushStamp::class))) {
            $this->container->get('doctrine.orm.entity_manager')->flush();
        }

        return $envelope;
    }

    public static function getSubscribedServices(): array
    {
        return [
            'doctrine.orm.entity_manager' => EntityManagerInterface::class,
        ];
    }
}
