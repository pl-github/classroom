<?php

namespace Code\AnalyzerBundle\Writer;

use Code\AnalyzerBundle\Model\ClassesModel;
use Code\BuildBundle\Build;

interface WriterInterface
{
    /**
     * Write build
     *
     * @param Build        $build
     * @param ClassesModel $classes
     */
    public function write(Build $build, ClassesModel $classes);
}
