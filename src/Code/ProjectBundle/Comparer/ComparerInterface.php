<?php

namespace Code\ProjectBundle\Comparer;

use Code\ProjectBundle\Entity\Revision;

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
