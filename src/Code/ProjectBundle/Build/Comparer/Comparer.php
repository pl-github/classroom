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
        $changeSet = new ChangeSet($to);

        $fromClasses = $from->getClasses();
        $toClasses = $to->getClasses();

        $classNames = array_unique(array_merge(array_keys($fromClasses->getClasses()), array_keys($toClasses->getClasses())));

        foreach ($classNames as $className)
        {
            $fromClass = $fromClasses->getClass($className);
            /* @var $fromClass \Code\ProjectBundle\Model\ClassModel */

            $toClass = $toClasses->getClass($className);
            /* @var $toClass \Code\ProjectBundle\Model\ClassModel */

            if (!$fromClass) {
                $changeSet->addChange(new NewClassChange($toClass));
            } elseif (!$toClass) {
                $changeSet->addChange(new ClassRemovedChange($fromClass));
            } elseif ($fromClass->getScore() != $toClass->getScore()) {
                $changeSet->addChange(new ScoreChange($toClass, $fromClass->getScore(), $toClass->getScore()));
            }
        }

        return $changeSet;
    }
}