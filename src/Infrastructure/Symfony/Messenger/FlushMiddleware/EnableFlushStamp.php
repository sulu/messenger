<?php

declare(strict_types=1);

namespace Sulu\Messenger\Infrastructure\Symfony\Messenger\FlushMiddleware;

use Symfony\Component\Messenger\Stamp\StampInterface;

/**
 * Marker stamp to enable DoctrineFlushMiddleware for envelopes with this stamp.
 */
class EnableFlushStamp implements StampInterface
{
}
