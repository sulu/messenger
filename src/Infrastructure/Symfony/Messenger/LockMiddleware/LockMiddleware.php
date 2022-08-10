<?php

declare(strict_types=1);

namespace Sulu\Messenger\Infrastructure\Symfony\Messenger\LockMiddleware;

use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;

class LockMiddleware implements MiddlewareInterface
{
    public function __construct(
        private LockFactory $lockFactory
    ) {
    }

    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        $locks = [];

        try {
            /** @var LockStamp[] $lockStamps */
            $lockStamps = $envelope->all(LockStamp::class);

            foreach ($lockStamps as $lockStamp) {
                $lock = $this->lockFactory->createLock(
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
}
