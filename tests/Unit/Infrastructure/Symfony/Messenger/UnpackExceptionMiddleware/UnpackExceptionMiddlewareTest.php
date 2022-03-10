<?php

declare(strict_types=1);

namespace Sulu\Messenger\Tests\Unit\Common\Infrastructure\Symfony\Messenger\UnpackExceptionMiddleware;

use PHPUnit\Framework\TestCase;
use Sulu\Messenger\Infrastructure\Symfony\Messenger\UnpackExceptionMiddleware\UnpackExceptionMiddleware;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;
use Symfony\Component\Messenger\Middleware\StackMiddleware;

/**
 * @covers \Sulu\Messenger\Infrastructure\Symfony\Messenger\UnpackExceptionMiddleware\UnpackExceptionMiddleware
 */
class UnpackExceptionMiddlewareTest extends TestCase
{
    private UnpackExceptionMiddleware $middleware;

    protected function setUp(): void
    {
        $this->middleware = new UnpackExceptionMiddleware();
    }

    public function testHandleDefault(): void
    {
        $envelope = $this->createEnvelope();
        $stack = $this->createStack();

        $this->assertSame(
            $envelope,
            $this->middleware->handle($envelope, $stack)
        );
    }

    public function testHandleException(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Specific unpacked exception.');

        $envelope = $this->createEnvelope();
        $stack = $this->createStack(function () use ($envelope) {
            throw new HandlerFailedException($envelope, [new \LogicException('Specific unpacked exception.')]);
        });

        $this->assertSame(
            $envelope,
            $this->middleware->handle($envelope, $stack)
        );
    }

    private function createEnvelope(): Envelope
    {
        return new Envelope(new \stdClass());
    }

    private function createStack(callable $handler = null): StackMiddleware
    {
        if (!$handler) {
            return new StackMiddleware([]);
        }

        $middleware = new class($handler) implements MiddlewareInterface {
            /**
             * @param callable $handler
             */
            public function __construct(private $handler)
            {
            }

            public function handle(Envelope $envelope, StackInterface $stack): Envelope
            {
                $handler = $this->handler;
                $handler();

                return $envelope;
            }
        };

        return new StackMiddleware($middleware);
    }
}
