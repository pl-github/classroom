<?php

namespace Code\AnalyzerBundle\Analyzer\Runner;

interface RunnerInterface
{
    /**
     * Parse phpcpd file
     *
     * @param string $sourceDirectory
     * @param string $workDirectory
     * @return Model
     * @throws \Exception
     */
    public function run($sourceDirectory, $workDirectory);
}
