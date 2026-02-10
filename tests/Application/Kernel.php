<?php

declare(strict_types=1);

/*
 * This file is part of Sulu.
 *
 * (c) Sulu GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Sulu\Messenger\Tests\Application;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Sulu\Messenger\Infrastructure\Symfony\HttpKernel\SuluMessengerBundle;
use Symfony\Bundle\DebugBundle\DebugBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    public function getProjectDir(): string
    {
        return __DIR__;
    }

    public function registerBundles(): iterable
    {
        $bundles = [
            FrameworkBundle::class => ['all' => true],
            DebugBundle::class => ['dev' => true, 'test' => true],
            DoctrineBundle::class => ['all' => true],
            SuluMessengerBundle::class => ['all' => true],
        ];

        foreach ($bundles as $class => $envs) {
            // @phpstan-ignore-next-line offsetAccess.invalidOffset
            if ($envs[$this->environment] ?? $envs['all'] ?? false) {
                yield new $class();
            }
        }
    }
}
