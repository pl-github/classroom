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

                $metrics = $pdependClass->getMetrics();

                $class->addMetric(new MetricModel('linesOfCode', $metrics['loc']));
                $class->addMetric(new MetricModel('logicalLinesOfCode', $metrics['lloc']));
                $class->addMetric(new MetricModel('executableLinesOfCode', $metrics['eloc']));

                $class->addMetric(new MetricModel('numberOfMethods', $metrics['nom']));

                $class->addMetric(new MetricModel('weightedMethodCount', $metrics['wmc']));
                $class->addMetric(new MetricModel('inheritedWeightedMethodCount', $metrics['wmci']));
                $class->addMetric(new MetricModel('nonPrivateWeightedMethodCount', $metrics['wmcnp']));

                $classes->addClass($class);

                foreach ($pdependClass->getMethods() as $pdependMethod)
                {
                    /* @var $pdependMethod PdependMethodModel */
                    $metrics = $pdependMethod->getMetrics();

                    if ($metrics['ccn'] > 5) {
                        $smell = new SmellModel('metrics', 'High method complexity', '', 1);
                        $class->addSmell($smell);
                    }
                }
            }
        }

        return $classes;
    }
}
