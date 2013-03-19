<?php

namespace Code\AnalyzerBundle\Analyzer;

use Code\AnalyzerBundle\Model\ResultModel;

interface AnalyzerInterface
{
    /**
     * Analyze
     *
     * @param ResultModel $result
     * @param string      $sourceDirectory
     * @param string      $workDirectory
     */
    public function analyze(ResultModel $result, $sourceDirectory, $workDirectory);
}
