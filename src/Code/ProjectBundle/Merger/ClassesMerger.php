<?php

namespace Code\ProjectBundle\Merger;

use Code\ProjectBundle\Model\ClassesModel;
use Code\ProjectBundle\Model\ClassModel;
use Code\ProjectBundle\Model\MetricModel;
use Code\ProjectBundle\Model\SmellModel;

class ClassesMerger
{
    /**
     * Merge classes models into new classes model
     * @param ClassesModel $from
     * @return ClassesModel
     * @thows \Exception
     */
    public function merge(ClassesModel $from)
    {
        $mergedClasses = new ClassesModel();

        foreach (func_get_args() as $from)
        {
            /* @var $from ClassesModel */

            if (!$from instanceof ClassesModel) {
                throw new \Exception('Not a ClassesModel');
            }

            foreach ($from->getClasses() as $class) {
                /* @var $class ClassModel */

                $className = $class->getName();

                if (!$mergedClasses->hasClass($className)) {
                    $intoClass = clone $class;

                    $mergedClasses->addClass($intoClass);
                } else {
                    $intoClass = $mergedClasses->getClass($className);
                }

                /* @var $intoClass ClassModel */

                foreach ($class->getSmells() as $smell) {
                    /* @var $smell SmellModel */

                    $intoSmell = clone $smell;

                    $intoClass->addSmell($intoSmell);
                }

                foreach ($class->getMetrics() as $metric) {
                    /* @var $metric MetricModel */

                    $intoMetric = clone $metric;

                    $intoClass->addMetric($intoMetric);
                }
            }
        }

        return $mergedClasses;
    }
}