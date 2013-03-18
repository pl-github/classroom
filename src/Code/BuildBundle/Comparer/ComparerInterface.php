<?php

namespace Code\BuildBundle\Comparer;

use Code\BuildBundle\Build;

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
