<?php

namespace Code\AnalyzerBundle;

interface ResultBuilderInterface
{
    /**
     * @param $sourceDirectory
     * @param $workDirectory
     */
    public function createResult($sourceDirectory, $workDirectory);
}