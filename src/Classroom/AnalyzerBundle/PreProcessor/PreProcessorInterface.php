<?php

namespace Classroom\AnalyzerBundle\PreProcessor;

use Classroom\AnalyzerBundle\Processor\ProcessorInterface;
use Classroom\AnalyzerBundle\Result\Result;

interface PreProcessorInterface extends ProcessorInterface
{
    /**
     * Preprocess result
     *
     * @param Result $result
     */
    public function process(Result $result);
}
