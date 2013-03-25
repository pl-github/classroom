<?php

namespace Code\AnalyzerBundle;

use Code\AnalyzerBundle\Result\Result;

interface ResultBuilderInterface
{
    /**
     * Build result
     *
     * @param string   $sourceDirectory
     * @param string   $workingDirectory
     * @param callable $logCallback
     * @return Result
     */
    public function buildResult($sourceDirectory, $workingDirectory, callable $logCallback = null);
}
