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

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @author Thomas Rabaix <thomas.rabaix@ekino.com>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * Generates the configuration tree.
     *
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('ekino_drupal', 'array');

        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('root')->isRequired()->end()
                ->scalarNode('logger')->defaultValue('ekino.drupal.logger.watchdog')->end()
                ->scalarNode('strategy_id')->defaultValue('ekino.drupal.delivery_strategy.symfony')->end()
                ->arrayNode('provider_keys')
                    ->prototype('scalar')->cannotBeEmpty()->end()
                ->end()
                ->arrayNode('entity_repositories')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('type')->cannotBeEmpty()->defaultValue('node')->end()
                            ->scalarNode('bundle')->end()
                            ->scalarNode('class')->cannotBeEmpty()->defaultValue('Ekino\Bundle\DrupalBundle\Entity\EntityRepository')->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('table_prefix')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('enabled')->defaultFalse()->end()
                        ->scalarNode('prefix')->defaultValue('symfony__')->end()
                        ->arrayNode('exclude')
                            ->prototype('scalar')->end()
                            ->defaultValue(array('users'))
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('session')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('refresh_cookie_lifetime')->defaultFalse()->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
