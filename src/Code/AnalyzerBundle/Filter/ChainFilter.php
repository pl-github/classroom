<?php

namespace Code\AnalyzerBundle\Filter;

use Code\AnalyzerBundle\Model\ResultModel;

class ChainFilter implements FilterInterface
{
    /**
     * @var FilterInterface[]
     */
    private $filters = array();

    /**
     * @param FilterInterface[] $filters
     */
    public function __construct(array $filters = array())
    {
        $this->filters = $filters;
    }

    /**
     * @inheritDoc
     */
    public function filter(ResultModel $result)
    {
        foreach ($this->filters as $filter) {
            $filter->filter($result);
        }
    }
}
