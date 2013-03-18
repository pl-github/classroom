<?php

namespace Code\CodeStyleBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * Generates the configuration tree builder.
     *
     * @return TreeBuilder The tree builder
     *
     * @throws \RuntimeException When using the deprecated 'charset' setting
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('code_code_style');

        $rootNode
            ->children()
                ->arrayNode('phpcs')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('executable')->defaultValue('%kernel.root_dir%/../bin/phpcs')->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
