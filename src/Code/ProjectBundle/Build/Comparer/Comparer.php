<?php

namespace Code\ProjectBundle\Build\Comparer;

use Code\ProjectBundle\Build\Build;

class Comparer implements ComparerInterface
{
    /**
     * @inheritDoc
     */
    public function compare(Build $from, Build $to)
    {
        $diff = new Diff();

        $fromClasses = $from->getClasses();
        $toClasses = $to->getClasses();

        foreach ($fromClasses->getClasses() as $fromClass)
        {
            $classname = $fromClass->getName();

            $toClass = $toClasses->getClass($classname);
        }

        return $diff;
    }
}