<?php

namespace Code\AnalyzerBundle\Writer;

use Code\AnalyzerBundle\Result\Result;

interface WriterInterface
{
    /**
     * Write result
     *
     * @param Result $result
     * @param string $filename
     * @return string
     */
    public function write(Result $result, $filename);

    /**
     * Return supported extension
     *
     * @param string $filename
     * @return boolean
     */
    public function supports($filename);
}
