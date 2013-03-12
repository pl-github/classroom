<?php

namespace Code\ProjectBundle\Build\Comparer;

use Code\ProjectBundle\Build\Build;

interface ComparerInterface
{
    /**
     * Compare two build
     *
     * @param Build $from
     * @param Build $to
     * @return Diff
     */
    public function compare(Build $from, Build $to);
}
