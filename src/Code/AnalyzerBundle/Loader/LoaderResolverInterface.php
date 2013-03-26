<?php

namespace Code\AnalyzerBundle\Loader;

interface LoaderResolverInterface
{
    /**
     * Resolve loader
     *
     * @param string $filename
     * @return LoaderInterface|false
     */
    public function resolve($filename);
}
