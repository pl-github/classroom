<?php

namespace Code\AnalyzerBundle\Analyzer\Parser;

use Code\AnalyzerBundle\Analyzer\Model\ModelInterface;

interface ParserInterface
{
    /**
     * Parse phpcpd file
     *
     * @param string $filename
     * @return ModelInterface
     */
    public function parse($filename);
}
