<?php

namespace Ekino\Bundle\DrupalBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\ConfigurationInterface;

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
                ->scalarNode('strategy_id')->defaultValue('ekino.drupal.delivery_strategy.symfony')->end()
            ->end();

        return $treeBuilder;
    }
}