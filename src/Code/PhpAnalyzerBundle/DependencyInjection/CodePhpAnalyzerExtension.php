<?php

namespace Code\PhpAnalyzerBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Config\FileLocator;

class CodePhpAnalyzerExtension extends Extension
{
    /**
     * Responds to the app.config configuration parameter.
     *
     * @param array            $configs
     * @param ContainerBuilder $container
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $loader->load('services.xml');
        $loader->load('pre_processors.xml');
        $loader->load('post_processors.xml');

        $loader->load('phpcs.xml');
        $container->setParameter('code.php_analyzer.phpcs.executable', $config['phpcs']['executable']);

        $loader->load('phpcpd.xml');
        $container->setParameter('code.php_analyzer.phpcpd.executable', $config['phpcpd']['executable']);

        $loader->load('phpmd.xml');
        $container->setParameter('code.php_analyzer.phpmd.executable', $config['phpmd']['executable']);

        $loader->load('pdepend.xml');
        $container->setParameter('code.php_analyzer.pdepend.executable', $config['pdepend']['executable']);
    }
}
