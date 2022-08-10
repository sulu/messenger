<?php

declare(strict_types=1);

namespace Sulu\Messenger\Infrastructure\Symfony\HttpKernel;

use RuntimeException;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ConfigurationExtensionInterface;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @codeCoverageIgnore
 */
class SuluMessengerBundle extends Bundle implements ExtensionInterface, PrependExtensionInterface, ConfigurationExtensionInterface, ConfigurationInterface
{
    final public const ALIAS = 'sulu_messenger';

    public function getPath(): string
    {
        return \dirname(__DIR__);
    }

    protected function createContainerExtension(): ExtensionInterface
    {
        return $this;
    }

    public function getAlias(): string
    {
        return self::ALIAS;
    }

    public function getXsdValidationBasePath(): bool
    {
        return false;
    }

    /**
     * @param array<string, mixed> $config
     */
    public function getConfiguration(array $config, ContainerBuilder $container): ConfigurationInterface
    {
        return $this;
    }

    public function prepend(ContainerBuilder $container): void
    {
        if (!$container->hasExtension('framework')) {
            throw new RuntimeException(\sprintf('The "%s" bundle requires "framework" bundle.', self::ALIAS));
        }

        $container->prependExtensionConfig(
            'framework',
            [
                'messenger' => [
                    'default_bus' => 'sulu_message_bus',
                    'buses' => [
                        'sulu_message_bus' => [
                            'middleware' => [
                                'sulu_messenger.unpack_exception_middleware',
                                'sulu_messenger.lock_middleware',
                                'sulu_messenger.doctrine_flush_middleware',
                            ],
                        ],
                    ],
                ],
            ],
        );
    }

    public function getConfigTreeBuilder(): TreeBuilder
    {
        return new TreeBuilder(self::ALIAS);
    }

    /**
     * @param array<string, mixed> $configs
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new PhpFileLoader($container, new FileLocator(\dirname(__DIR__, 4) . '/config'));

        $loader->load('services.php');
    }
}
