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
                ->arrayNode('phpcpd')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('executable')->defaultValue('%kernel.root_dir%/../bin/phpcpd')->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
