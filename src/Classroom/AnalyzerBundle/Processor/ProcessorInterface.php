<?php

namespace Classroom\AnalyzerBundle\Processor;

use Classroom\AnalyzerBundle\Result\Result;

interface ProcessorInterface
{
    /**
     * Process result
     *
     * @param Result $result
     */
    public function process(Result $result);
}
