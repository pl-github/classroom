<?php

namespace Code\AnalyzerBundle;

use Code\AnalyzerBundle\DependencyInjection\Compiler\AddAnalyzersPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class CodeAnalyzerBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new AddAnalyzersPass());
    }
}
