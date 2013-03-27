<?php

namespace Classroom\PhpAnalyzerBundle\DependencyInjection;

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
        $rootNode = $treeBuilder->root('classroom_php_analyzer');

        $rootNode
            ->children()
                ->arrayNode('phpcs')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('executable')->defaultValue('%kernel.root_dir%/../bin/phpcs')->end()
                    ->end()
                ->end()
                ->arrayNode('phpcpd')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('executable')->defaultValue('%kernel.root_dir%/../bin/phpcpd')->end()
                    ->end()
                ->end()
                ->arrayNode('phpmd')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('executable')->defaultValue('%kernel.root_dir%/../bin/phpmd')->end()
                    ->end()
                ->end()
                ->arrayNode('pdepend')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('executable')->defaultValue('%kernel.root_dir%/../bin/pdepend')->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
