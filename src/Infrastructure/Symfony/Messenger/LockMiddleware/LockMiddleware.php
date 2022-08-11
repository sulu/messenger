<?php

declare(strict_types=1);

namespace Sulu\Messenger\Infrastructure\Symfony\Messenger\LockMiddleware;

use Psr\Container\ContainerInterface;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;
use Symfony\Contracts\Service\ServiceSubscriberInterface;

class LockMiddleware implements MiddlewareInterface, ServiceSubscriberInterface
{
    public function __construct(private ContainerInterface $container)
    {
    }

    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        $locks = [];

        try {
            /** @var LockStamp[] $lockStamps */
            $lockStamps = $envelope->all(LockStamp::class);

            foreach ($lockStamps as $lockStamp) {
                $lock = $this->container->get('lock.factory')->createLock(
                    $lockStamp->getResource(),
                    $lockStamp->getTtl(),
                    $lockStamp->getAutoRelease(),
                );

                $lock->acquire(true);
                $locks[] = $lock;
            }

            $envelope = $stack->next()->handle($envelope, $stack);
        } finally {
            foreach ($locks as $lock) {
                $lock->release();
            }
        }

        return $envelope;
    }

    public static function getSubscribedServices(): array
    {
        return [
            'lock.factory' => LockFactory::class,
        ];
    }
}
