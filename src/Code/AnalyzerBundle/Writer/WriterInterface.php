<?php

namespace Code\AnalyzerBundle\Writer;

use Code\AnalyzerBundle\Model\ResultModel;

interface WriterInterface
{
    /**
     * Write result
     *
     * @param ResultModel $result
     * @param string      $targetDir
     * @param string      $baseFilename
     * @return string
     */
    public function write(ResultModel $result, $targetDir, $baseFilename);
}
