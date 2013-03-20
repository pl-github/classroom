<?php

namespace Code\AnalyzerBundle\Writer;

use Code\AnalyzerBundle\Model\ResultModel;

interface WriterInterface
{
    /**
     * Write result
     *
     * @param ResultModel $result
     * @param string      $filename
     * @return string
     */
    public function write(ResultModel $result, $filename);

    /**
     * Return supported extension
     *
     * @param string $filename
     * @return boolean
     */
    public function supports($filename);
}
