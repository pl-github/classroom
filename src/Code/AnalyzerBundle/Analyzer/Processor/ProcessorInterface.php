<?php

namespace Code\AnalyzerBundle\Analyzer\Processor;

use Code\AnalyzerBundle\Model\ClassesModel;
use Code\AnalyzerBundle\ResultBuilder;

interface ProcessorInterface
{
    /**
     * Process file
     *
     * @param ResultBuilder $resultBuilder
     * @param string        $filename
     */
    public function process(ResultBuilder $resultBuilder, $filename);
}
