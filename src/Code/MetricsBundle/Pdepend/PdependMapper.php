<?php

namespace Code\MetricsBundle\Pdepend;

use Code\ProjectBundle\Model\ClassesModel;
use Code\ProjectBundle\Model\ClassModel;
use Code\ProjectBundle\Model\MetricModel;
use Code\MetricsBundle\Pdepend\Model\ClassModel as PdependClassModel;
use Code\MetricsBundle\Pdepend\Model\MethodModel as PdependMethodModel;
use Code\MetricsBundle\Pdepend\Model\MetricsModel as PdependMetricsModel;
use Code\MetricsBundle\Pdepend\Model\PackageModel as PdependPackageModel;
use Code\ProjectBundle\Model\SmellModel;

class PdependMapper
{
    /**
     * Map pdepend models to class models
     *
     * @param PdependMetricsModel $pdependMetrics
     * @return ClassesModel
     */
    public function map(PdependMetricsModel $pdependMetrics)
    {
        $classes = new ClassesModel();

        foreach ($pdependMetrics->getPackages() as $pdependPackage) {
            /* @var $pdependPackage PdependPackageModel */

            foreach ($pdependPackage->getClasses() as $pdependClass) {
                /* @var $pdependClass PdependClassModel */

                $class = new ClassModel($pdependPackage->getName() . '\\' . $pdependClass->getName());

                $classMetrics = $pdependClass->getMetrics();

                $class->addMetric(new MetricModel('linesOfCode', $classMetrics['loc']));
                $class->addMetric(new MetricModel('logicalLinesOfCode', $classMetrics['lloc']));
                $class->addMetric(new MetricModel('executableLinesOfCode', $classMetrics['eloc']));

                $class->addMetric(new MetricModel('numberOfMethods', $classMetrics['nom']));

                $class->addMetric(new MetricModel('weightedMethodCount', $classMetrics['wmc']));
                $class->addMetric(new MetricModel('inheritedWeightedMethodCount', $classMetrics['wmci']));
                $class->addMetric(new MetricModel('nonPrivateWeightedMethodCount', $classMetrics['wmcnp']));

                $classes->addClass($class);

                if ($classMetrics['wmc'] > 5) {
                    $smell = new SmellModel('metrics', 'High overall complexity', '', 1);
                    $class->addSmell($smell);
                }

                foreach ($pdependClass->getMethods() as $pdependMethod)
                {
                    /* @var $pdependMethod PdependMethodModel */
                    $methodMetrics = $pdependMethod->getMetrics();

                    if ($methodMetrics['ccn'] > 5) {
                        $smell = new SmellModel('metrics', 'High method complexity', '', 1);
                        $class->addSmell($smell);
                    }
                }
            }
        }

        return $classes;
    }
}
