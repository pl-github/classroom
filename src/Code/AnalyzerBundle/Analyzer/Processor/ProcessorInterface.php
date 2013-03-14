<?php

namespace Code\AnalyzerBundle\Analyzer\Processor;

use Code\AnalyzerBundle\Model\ClassesModel;

interface ProcessorInterface
{
    /**
     * Process file
     *
     * @param string $filename
     * @return ClassesModel
     */
    public function process($filename);
}
