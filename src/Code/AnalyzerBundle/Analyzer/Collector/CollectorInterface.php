<?php

namespace Code\AnalyzerBundle\Analyzer\Collector;

interface CollectorInterface
{
    /**
     * Collect analysis data
     *
     * @param string $sourceDirectory
     * @param string $workDirectory
     * @return mixed
     * @throws \Exception
     */
    public function collect($sourceDirectory, $workDirectory);
}
