<?php

namespace Code\AnalyzerBundle\PreProcessor;

use Code\AnalyzerBundle\Processor\ProcessorInterface;
use Code\AnalyzerBundle\Result\Result;

interface PreProcessorInterface extends ProcessorInterface
{
    /**
     * Preprocess result
     *
     * @param Result $result
     */
    public function process(Result $result);
}