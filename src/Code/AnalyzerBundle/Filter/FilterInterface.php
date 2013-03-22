<?php

namespace Code\AnalyzerBundle\Filter;

use Code\AnalyzerBundle\Model\ResultModel;

interface FilterInterface
{
    /**
     * Filter result
     *
     * @param ResultModel $result
     */
    public function filter(ResultModel $result);
}
