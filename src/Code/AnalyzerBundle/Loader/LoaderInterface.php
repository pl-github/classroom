<?php

namespace Code\AnalyzerBundle\Loader;

use Code\AnalyzerBundle\Result\Result;

interface LoaderInterface
{
    /**
     * Load result
     *
     * @param string $filename
     * @return Result
     */
    public function load($filename);

    /**
     * Return supported extension
     *
     * @param string $filename
     * @return boolean
     */
    public function supports($filename);
}
