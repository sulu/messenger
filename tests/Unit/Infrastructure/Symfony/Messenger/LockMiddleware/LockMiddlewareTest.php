<?php

declare(strict_types=1);

namespace Sulu\Messenger\Tests\Unit\Common\Infrastructure\Symfony\Messenger\LockMiddleware;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Sulu\Messenger\Infrastructure\Symfony\Messenger\LockMiddleware\LockMiddleware;
use Sulu\Messenger\Infrastructure\Symfony\Messenger\LockMiddleware\LockStamp;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Lock\LockInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\StackMiddleware;

/**
 * @covers \Sulu\Messenger\Infrastructure\Symfony\Messenger\LockMiddleware\LockMiddleware
 */
class LockMiddlewareTest extends TestCase
{
    use ProphecyTrait;

    private LockMiddleware $middleware;

    /**
     * @var ObjectProphecy<LockFactory>
     */
    private ObjectProphecy $lockFactory;

    protected function setUp(): void
    {
        $this->lockFactory = $this->prophesize(LockFactory::class);
        $this->middleware = new LockMiddleware(
            $this->lockFactory->reveal()
        );
    }

    public function testHandleWithoutStamp(): void
    {
        $envelope = $this->createEnvelope();
        $stack = $this->createStack();

        $this->lockFactory->createLock(Argument::cetera())
            ->shouldNotBeCalled();

        $this->assertSame(
            $envelope,
            $this->middleware->handle($envelope, $stack)
        );
    }

    public function testHandleWithStamp(): void
    {
        $envelope = $this->createEnvelope();
        $envelope = $envelope->with(new LockStamp('test', 30, true));
        $stack = $this->createStack();

        $lock = $this->prophesize(LockInterface::class);
        $lock->acquire(true)
            ->shouldBeCalled();
        $lock->release()
            ->shouldBeCalled();

        $this->lockFactory->createLock('test', 30, true)
            ->willReturn($lock->reveal())
            ->shouldBeCalled();

        $this->assertSame(
            $envelope,
            $this->middleware->handle($envelope, $stack)
        );
    }

    private function createEnvelope(): Envelope
    {
        return new Envelope(new \stdClass());
    }

    private function createStack(): StackMiddleware
    {
        return new StackMiddleware([]);
    }
}
