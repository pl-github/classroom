<?php

namespace Code\AnalyzerBundle\Analyzer;

use Code\AnalyzerBundle\Model\ResultModel;

class ChainAnalyzer implements AnalyzerInterface
{
    /**
     * @var array
     */
    private $analyzers;

    /**
     * @param AnalyzerInterface[] $analyzers
     */
    public function __construct(array $analyzers = array())
    {
        $this->analyzers = $analyzers;
    }

    /**
     * @inheritDoc
     */
    public function analyze(ResultModel $result, $sourceDirectory, $workDirectory)
    {
        foreach ($this->analyzers as $analyzer) {
            /* @var $analyzer AnalyzerInterface */

            $analyzer->analyze($result, $sourceDirectory, $workDirectory);
        }
    }
}
