<?php

declare(strict_types=1);

namespace Sulu\Messenger\Infrastructure\Symfony\Messenger\LockMiddleware;

use Symfony\Component\Messenger\Stamp\StampInterface;

/**
 * Stamp to lock a process handling to avoid race conditions.
 */
class LockStamp implements StampInterface
{
    public function __construct(
        private readonly string $resource,
        private readonly ?float $ttl = 300.0,
        private readonly bool $autoRelease = true,
    ) {
    }

    public function getResource(): string
    {
        return $this->resource;
    }

    public function getTtl(): ?float
    {
        return $this->ttl;
    }

    public function getAutoRelease(): bool
    {
        return $this->autoRelease;
    }
}
