<?php

namespace Code\ProjectBundle\Build\Comparer;

use Code\AnalyzerBundle\Model\ClassModel;
use Code\ProjectBundle\Build\Build;
use Code\ProjectBundle\Change\ChangeSet;
use Code\ProjectBundle\Change\NewClassChange;
use Code\ProjectBundle\Change\ClassRemovedChange;
use Code\ProjectBundle\Change\ScoreChange;

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

        $classNames = array_unique(
            array_merge(
                array_keys($fromClasses->getClasses()),
                array_keys($toClasses->getClasses())
            )
        );

        foreach ($classNames as $className) {
            $fromClass = $fromClasses->getClass($className);
            /* @var $fromClass ClassModel */

            $toClass = $toClasses->getClass($className);
            /* @var $toClass ClassModel */

            if (!$fromClass) {
                $change = new NewClassChange($toClass->getFullQualifiedName(), $toClass->getScore());
                $changeSet->addChange($change);
            } elseif (!$toClass) {
                $change = new ClassRemovedChange($fromClass->getFullQualifiedName());
                $changeSet->addChange($change);
            } elseif ($fromClass->getScore() != $toClass->getScore()) {
                $change = new ScoreChange(
                    $toClass->getFullQualifiedName(),
                    $fromClass->getScore(),
                    $toClass->getScore()
                );
                $changeSet->addChange($change);
            }
        }

        return $changeSet;
    }
}
