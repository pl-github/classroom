<?php

namespace Code\AnalyzerBundle\Loader;

use Code\AnalyzerBundle\Model\ResultModel;

interface LoaderInterface
{
    /**
     * Load result
     *
     * @param string $filename
     * @return ResultModel
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
