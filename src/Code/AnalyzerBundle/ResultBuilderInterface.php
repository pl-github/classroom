<?php

namespace Code\AnalyzerBundle;

interface ResultBuilderInterface
{
    /**
     * Build
     *
     * @param string $sourceDirectory
     * @param string $workDirectory
     */
    public function build($sourceDirectory, $workDirectory);
}
