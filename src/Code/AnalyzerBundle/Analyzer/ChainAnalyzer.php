<?php

namespace Code\AnalyzerBundle\Analyzer;

use Code\AnalyzerBundle\Merger\ClassesMerger;

class ChainAnalyzer implements AnalyzerInterface
{
    /**
     * @var ClassesMerger
     */
    private $merger;

    /**
     * @var array
     */
    private $analyzers;

    /**
     * @param ClassesMerger $merger
     * @param array         $analyzers
     */
    public function __construct(ClassesMerger $merger, array $analyzers = array())
    {
        $this->merger = $merger;
        $this->analyzers = $analyzers;
    }

    /**
     * @inheritDoc
     */
    public function analyze($sourceDirectory, $workDirectory)
    {
        $results = array();

        foreach ($this->analyzers as $analyzer) {
            /* @var $analyzer AnalyzerInterface */
            $results[] = $analyzer->analyze($sourceDirectory, $workDirectory);
        }

        return $this->merger->merge($results);
    }
}
