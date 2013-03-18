<?php

namespace Code\AnalyzerBundle\Writer;

use Code\AnalyzerBundle\Model\ResultModel;

interface WriterInterface
{
    /**
     * Write result
     *
     * @param ResultModel $result
     */
    public function write(ResultModel $result);
}
