<?php

namespace Code\AnalyzerBundle\Analyzer\Processor;

use Code\AnalyzerBundle\Model\ClassesModel;
use Code\AnalyzerBundle\ResultBuilderInterface;

interface ProcessorInterface
{
    /**
     * Process file
     *
     * @param ResultBuilderInterface $resultBuilder
     * @param string                 $filename
     */
    public function process(ResultBuilderInterface $resultBuilder, $filename);
}
