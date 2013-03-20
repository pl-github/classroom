<?php

namespace Code\PhpAnalyzerBundle;

use Code\AnalyzerBundle\Analyzer\AnalyzerInterface;
use Code\AnalyzerBundle\Filter\FilterInterface;
use Code\AnalyzerBundle\Model\ResultModel;
use Code\AnalyzerBundle\ResultBuilderInterface;

class ResultBuilder implements ResultBuilderInterface
{
    /**
     * @var AnalyzerInterface
     */
    private $analyzer;

    /**
     * @var FilterInterface
     */
    private $filter;

    /**
     * @param AnalyzerInterface $analyzer
     * @param FilterInterface $filter
     */
    public function __construct(AnalyzerInterface $analyzer, FilterInterface $filter)
    {
        $this->analyzer = $analyzer;
        $this->filter = $filter;
    }

    /**
     * @inheritDoc
     */
    public function build($sourceDirectory, $workDirectory)
    {
        $result = new ResultModel();

        $this->analyzer->analyze($result, $sourceDirectory, $workDirectory);
        $this->filter->filter($result);

        return $result;
    }
}
