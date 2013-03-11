<?php

namespace Code\AnalyzerBundle\Analyzer;

interface AnalyzerInterface
{
    /**
     * Analyze
     *
     * @param string $sourceDirectory
     * @param string $workDirectory
     * @return mixed
     */
    public function analyze($sourceDirectory, $workDirectory);
}
