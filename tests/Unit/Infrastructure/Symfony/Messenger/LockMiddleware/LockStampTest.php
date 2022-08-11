<?php

declare(strict_types=1);

namespace Sulu\Messenger\Infrastructure\Symfony\Messenger\LockMiddleware;

use PHPUnit\Framework\TestCase;

/**
 * @covers \Sulu\Messenger\Infrastructure\Symfony\Messenger\LockMiddleware\LockStamp
 */
class LockStampTest extends TestCase
{
    public function testGetResource(): void
    {
        $model = $this->createInstance(['resource' => 'Resource']);
        $this->assertSame('Resource', $model->getResource());
    }

    public function testGetTtl(): void
    {
        $model = $this->createInstance(['ttl' => 30.0]);
        $this->assertSame(30.0, $model->getTtl());
    }

    public function testGetAutoRelease(): void
    {
        $model = $this->createInstance(['autoRelease' => true]);
        $this->assertTrue($model->getAutoRelease());
    }

    /**
     * @param array{
     *    resource?: string,
     *    ttl?: float|null,
     *    autoRelease?: bool,
     * } $data
     */
    public function createInstance(array $data = []): LockStamp
    {
        return new LockStamp($data['resource'] ?? 'Resource', $data['ttl'] ?? 300.0, $data['autoRelease'] ?? true);
    }
}
