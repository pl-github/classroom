<?php

namespace Code\AnalyzerBundle\Analyzer\Processor;

use Code\AnalyzerBundle\Model\ResultModel;

interface ProcessorInterface
{
    /**
     * Process file
     *
     * @param ResultModel $result
     * @param string      $filename
     */
    public function process(ResultModel $result, $filename);
}
