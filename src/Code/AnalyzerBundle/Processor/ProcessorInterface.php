<?php

namespace Code\AnalyzerBundle\Processor;

use Code\AnalyzerBundle\Result\Result;

interface ProcessorInterface
{
    /**
     * Process result
     *
     * @param Result $result
     */
    public function process(Result $result);
}
