<?php

declare(strict_types=1);

namespace Sulu\Messenger\Infrastructure\Symfony\Messenger\FlushMiddleware;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;

class DoctrineFlushMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        $envelope = $stack->next()->handle($envelope, $stack);

        // flush unit-of-work to the database after the root message was handled successfully
        if (!empty($envelope->all(EnableFlushStamp::class))) {
            $this->entityManager->flush();
        }

        return $envelope;
    }
}
