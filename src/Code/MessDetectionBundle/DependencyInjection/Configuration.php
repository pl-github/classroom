<?php

namespace Code\MessDetectionBundle\DependencyInjection;

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
        $rootNode = $treeBuilder->root('mess_detection');

        $rootNode
            ->children()
                ->arrayNode('phpmd')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('executable')->defaultValue('phpmd')->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
