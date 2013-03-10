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
use Code\ProjectBundle\ClassnameService;

class PdependMapper
{
    /**
     * @var ClassnameService
     */
    private $classnameService;

    /**
     * @param ClassnameService $classnameService
     */
    public function __construct(ClassnameService $classnameService)
    {
        $this->classnameService = $classnameService;
    }

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

                $className = $pdependPackage->getName() . '\\' . $pdependClass->getName();
                $class = new ClassModel($className);

                $classMetrics = $pdependClass->getMetrics();

                $lines = $classMetrics['loc'];
                $linesOfCode = $classMetrics['eloc'];
                $methods = $classMetrics['nom'];
                $linesOfCodePerMethod = $methods ? $linesOfCode / $methods : 0;
                $complexity = $classMetrics['wmcnp'];
                $complexityPerMethod = $methods ? $complexity / $methods : 0;

                $class->addMetric(new MetricModel('lines', $lines));
                $class->addMetric(new MetricModel('linesOfCode', $linesOfCode));

                $class->addMetric(new MetricModel('methods', $methods));
                $class->addMetric(new MetricModel('linesOfCodePerMethod', $linesOfCodePerMethod));

                $class->addMetric(new MetricModel('complexity', $complexity));
                $class->addMetric(new MetricModel('complexityPerMethod', $complexityPerMethod));

                $classes->addClass($class);

                $file = $pdependClass->getFile();


                if ($classMetrics['wmc'] > 7) {
                    $classSource = $this->classnameService->getClassSource($file, $className);

                    $smell = new SmellModel('metrics', 'High overall complexity', $classSource, 1);
                    $class->addSmell($smell);
                }

                foreach ($pdependClass->getMethods() as $pdependMethod)
                {
                    /* @var $pdependMethod PdependMethodModel */

                    $methodName = $pdependMethod->getName();

                    $methodMetrics = $pdependMethod->getMetrics();

                    if ($methodMetrics['ccn'] > 7) {
                        $methodSource = $this->classnameService->getMethodSource($file, $className, $methodName);

                        $smell = new SmellModel('metrics', 'Complex method ' . $methodName . '()', $methodSource, 1);
                        $class->addSmell($smell);
                    }
                }
            }
        }

        return $classes;
    }
}
