<?php

namespace Code\CopyPasteDetectionBundle\DependencyInjection;

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
        $rootNode = $treeBuilder->root('code_copy_paste_detection');

        $rootNode
            ->children()
                ->arrayNode('duplication')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('phpcpd')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('executable')->defaultValue('phpcpd')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
