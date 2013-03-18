<?php

namespace Code\AnalyzerBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class AddAnalyzersPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('code.analyzer.chain_analyzer')) {
            return;
        }

        $analyzers = array();
        foreach ($container->findTaggedServiceIds('code.analyzer') as $serviceId => $tag) {
            $analyzers[] = new Reference($serviceId);
        }

        $container->getDefinition('code.analyzer.chain_analyzer')->replaceArgument(0, $analyzers);
    }
}
