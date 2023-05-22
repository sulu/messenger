<?php

declare(strict_types=1);

namespace Sulu\Messenger\Tests\Unit\Common\Infrastructure\Symfony\Messenger\FlushMiddleware;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Sulu\Messenger\Infrastructure\Symfony\Messenger\FlushMiddleware\DoctrineFlushMiddleware;
use Sulu\Messenger\Infrastructure\Symfony\Messenger\FlushMiddleware\EnableFlushStamp;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\StackMiddleware;

/**
 * @covers \Sulu\Messenger\Infrastructure\Symfony\Messenger\FlushMiddleware\DoctrineFlushMiddleware
 */
class DoctrineFlushMiddlewareTest extends TestCase
{
    use ProphecyTrait;

    private DoctrineFlushMiddleware $middleware;

    /**
     * @var ObjectProphecy<EntityManagerInterface>
     */
    private ObjectProphecy $entityManager;

    protected function setUp(): void
    {
        $this->entityManager = $this->prophesize(EntityManagerInterface::class);
        $container = new Container();
        $container->set('doctrine.orm.entity_manager', $this->entityManager->reveal());

        $this->middleware = new DoctrineFlushMiddleware(
            $container,
        );
    }

    public function testHandleWithoutStamp(): void
    {
        $envelope = $this->createEnvelope();
        $stack = $this->createStack();

        $this->entityManager->flush()
            ->shouldNotBeCalled();

        $this->assertSame(
            $envelope,
            $this->middleware->handle($envelope, $stack),
        );
    }

    public function testHandleWithStamp(): void
    {
        $envelope = $this->createEnvelope();
        $envelope = $envelope->with(new EnableFlushStamp());
        $stack = $this->createStack();

        $this->entityManager->flush()
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
                'doctrine.orm.entity_manager' => EntityManagerInterface::class,
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
