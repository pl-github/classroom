<?php

namespace Code\AnalyzerBundle\Loader;

use Code\AnalyzerBundle\Model\ResultModel;
use Code\AnalyzerBundle\Serializer\SerializerInterface;

class DelegatingLoader implements LoaderInterface
{
    /**
     * @var LoaderResolverInterface
     */
    private $loaderResolver;

    /**
     * @param LoaderResolverInterface $loaderResolver
     */
    public function __construct(LoaderResolverInterface $loaderResolver)
    {
        $this->loaderResolver = $loaderResolver;
    }

    /**
     * @inheritDoc
     */
    public function load($filename)
    {
        $loader = $this->loaderResolver->resolve($filename);

        if (false === $loader) {
            throw new \Exception('No suitable loader found');
        }

        return $loader->load($filename);
    }

    /**
     * @inheritDoc
     */
    public function supports($filename)
    {
        return false !== $this->loaderResolver->resolve($filename);
    }
}
