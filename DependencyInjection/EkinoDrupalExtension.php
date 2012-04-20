<?php

/*
 * This file is part of the Ekino Drupal package.
 *
 * (c) 2011 Ekino
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ekino\Bundle\DrupalBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Config\Definition\Processor;

/**
 * @author Thomas Rabaix <thomas.rabaix@ekino.com>
 */
class EkinoDrupalExtension extends Extension
{
    /**
     * @param array $config
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $processor = new Processor();
        $config = $processor->processConfiguration($configuration, $config);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');
        $loader->load('user_hook.xml');

        $container->getDefinition('ekino.drupal')
            ->replaceArgument(0, $config['root'])
        ;

        $container->getDefinition('ekino.drupal.request_listener')
            ->replaceArgument(1, new Reference($config['strategy_id']))
        ;

        $container->getDefinition('ekino.drupal.user_registration_hook')
            ->replaceArgument(2, $config['provider_keys'])
        ;

        $container->setAlias('logger', $config['logger']);
    }
}