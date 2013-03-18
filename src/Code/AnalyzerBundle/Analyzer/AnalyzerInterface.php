<?php

namespace Code\AnalyzerBundle\Analyzer;

use Code\AnalyzerBundle\ResultBuilder;

interface AnalyzerInterface
{
    /**
     * Analyze
     *
     * @param ResultBuilder $resultBuilder
     * @param string        $sourceDirectory
     * @param string        $workDirectory
     */
    public function analyze(ResultBuilder $resultBuilder, $sourceDirectory, $workDirectory);
}
