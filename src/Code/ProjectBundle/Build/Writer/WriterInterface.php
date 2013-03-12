<?php

namespace Code\ProjectBundle\Build\Writer;

use Code\ProjectBundle\Build\Build;

interface WriterInterface
{
    /**
     * Write build
     *
     * @param Build $build
     */
    public function write(Build $build);
}
