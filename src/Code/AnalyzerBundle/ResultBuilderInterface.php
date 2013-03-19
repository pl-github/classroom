<?php

namespace Code\AnalyzerBundle;

use Code\AnalyzerBundle\Analyzer\AnalyzerInterface;

interface ResultBuilderInterface
{
    /**
     * @param AnalyzerInterface $analyzer
     * @param string            $sourceDirectory
     * @param string            $workDirectory
     */
    public function bla(AnalyzerInterface $analyzer, $sourceDirectory, $workDirectory);
}
