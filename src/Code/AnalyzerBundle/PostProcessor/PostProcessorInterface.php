<?php

namespace Code\AnalyzerBundle\PostProcessor;

use Code\AnalyzerBundle\Processor\ProcessorInterface;
use Code\AnalyzerBundle\Result\Result;

interface PostProcessorInterface extends ProcessorInterface
{
    /**
     * Postprocess result
     *
     * @param Result $result
     */
    public function process(Result $result);
}
