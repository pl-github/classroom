<?php

namespace Code\AnalyzerBundle\Loader;

class LoaderResolver implements LoaderResolverInterface
{
    /**
     * @var LoaderInterface[]
     */
    private $loaders;

    /**
     * @param LoaderInterface[] $loaders
     */
    public function __construct(array $loaders)
    {
        $this->loaders = $loaders;
    }

    /**
     * @inheritDoc
     */
    public function resolve($filename)
    {
        foreach ($this->loaders as $loader) {
            if ($loader->supports($filename)) {
                return $loader;
            }
        }

        return false;
    }
}
