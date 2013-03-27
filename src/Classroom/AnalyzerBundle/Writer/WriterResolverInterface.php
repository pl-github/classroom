<?php

namespace Classroom\AnalyzerBundle\Writer;

interface WriterResolverInterface
{
    /**
     * Resolve writer
     *
     * @param string $filename
     * @return WriterInterface|false
     */
    public function resolve($filename);
}
