<?php

declare(strict_types=1);

namespace Sulu\Messenger\Tests\Unit\Common\Infrastructure\Symfony\Messenger\LockMiddleware;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Sulu\Messenger\Infrastructure\Symfony\Messenger\LockMiddleware\LockMiddleware;
use Sulu\Messenger\Infrastructure\Symfony\Messenger\LockMiddleware\LockStamp;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Lock\SharedLockInterface;
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
        $container = new Container();
        $container->set('lock.factory', $this->lockFactory->reveal());

        $this->middleware = new LockMiddleware(
            $container,
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
            $this->middleware->handle($envelope, $stack),
        );
    }

    public function testHandleWithStamp(): void
    {
        $envelope = $this->createEnvelope();
        $envelope = $envelope->with(new LockStamp('test', 30, true));
        $stack = $this->createStack();

        $lock = $this->prophesize(SharedLockInterface::class);
        $lock->acquire(true)
            ->shouldBeCalled();
        $lock->release()
            ->shouldBeCalled();

        $this->lockFactory->createLock('test', 30, true)
            ->willReturn($lock->reveal())
            ->shouldBeCalled();

        $this->assertSame(
            $envelope,
            $this->middleware->handle($envelope, $stack),
        );
    }

    public function testGetSubscribedServices(): void
    {
        $this->assertSame(
            [
                'lock.factory' => LockFactory::class,
            ],
            $this->middleware->getSubscribedServices(),
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
