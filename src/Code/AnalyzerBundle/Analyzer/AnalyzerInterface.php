<?php

namespace Code\AnalyzerBundle\Analyzer;

use Code\AnalyzerBundle\ResultBuilderInterface;

interface AnalyzerInterface
{
    /**
     * Analyze
     *
     * @param ResultBuilderInterface $resultBuilder
     * @param string                 $sourceDirectory
     * @param string                 $workDirectory
     */
    public function analyze(ResultBuilderInterface $resultBuilder, $sourceDirectory, $workDirectory);
}
