<?php

namespace HHH\DependencyInjection;

use Exception;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/** @codeCoverageIgnore */
class HHHExtension extends Extension
{
    /**
     * @param array<int|string, mixed> $configs
     * @param ContainerBuilder $container
     * @return void
     * @throws Exception
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yaml');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('HHH.config', $config);
    }
}
