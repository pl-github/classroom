<?php

namespace Code\AnalyzerBundle\Merger;

use Code\AnalyzerBundle\Model\ClassesModel;
use Code\AnalyzerBundle\Model\ClassModel;
use Code\AnalyzerBundle\Model\MetricModel;
use Code\AnalyzerBundle\Model\SmellModel;

class ClassesMerger
{
    /**
     * Merge classes models into new classes model
     * Input arguments can be either an array with ClassesModels, or multiple arguments.
     *
     * @param mixed $from
     * @throws \InvalidArgumentException
     * @return ClassesModel
     */
    public function merge($from)
    {
        $mergedClasses = new ClassesModel();

        if (func_num_args() == 1 && is_array(func_get_arg(0))) {
            $args = func_get_arg(0);
        } elseif (func_num_args() > 1) {
            $args = func_get_args();
        } else {
            throw new \InvalidArgumentException('Need multiple input arguments, or a single array.');
        }

        foreach ($args as $from) {
            /* @var $from ClassesModel */

            if (!$from instanceof ClassesModel) {
                throw new \InvalidArgumentException('Not a ClassesModel');
            }

            foreach ($from->getClasses() as $class) {
                /* @var $class ClassModel */

                $className = $class->getFullQualifiedName();

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
