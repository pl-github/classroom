<?php

namespace Classroom\ProjectBundle\Comparer;

use Classroom\ProjectBundle\Entity\Revision;

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
