<?php

namespace Classroom\AnalyzerBundle;

use Classroom\AnalyzerBundle\Result\Result;

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
