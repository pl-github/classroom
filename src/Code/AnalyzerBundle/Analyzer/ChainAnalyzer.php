<?php

namespace Code\AnalyzerBundle\Analyzer;

use Code\AnalyzerBundle\ResultBuilder;

class ChainAnalyzer implements AnalyzerInterface
{
    /**
     * @var array
     */
    private $analyzers;

    /**
     * @param array $analyzers
     */
    public function __construct(array $analyzers = array())
    {
        $this->analyzers = $analyzers;
    }

    /**
     * @inheritDoc
     */
    public function analyze(ResultBuilder $resultBuilder, $sourceDirectory, $workDirectory)
    {
        foreach ($this->analyzers as $analyzer) {
            /* @var $analyzer AnalyzerInterface */

            $analyzer->analyze($resultBuilder, $sourceDirectory, $workDirectory);
        }
    }
}
