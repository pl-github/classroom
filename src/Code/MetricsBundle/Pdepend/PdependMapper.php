<?php

namespace Code\MetricsBundle\Pdepend;

use Code\AnalyzerBundle\Analyzer\Mapper\MapperInterface;
use Code\AnalyzerBundle\Analyzer\Model\ModelInterface;
use Code\AnalyzerBundle\Model\ClassesModel;
use Code\AnalyzerBundle\Model\ClassModel;
use Code\AnalyzerBundle\Model\MetricModel;
use Code\AnalyzerBundle\Model\SmellModel;
use Code\AnalyzerBundle\ReflectionService;
use Code\MetricsBundle\Pdepend\Model\ClassModel as PdependClassModel;
use Code\MetricsBundle\Pdepend\Model\MethodModel as PdependMethodModel;
use Code\MetricsBundle\Pdepend\Model\MetricsModel as PdependMetricsModel;
use Code\MetricsBundle\Pdepend\Model\PackageModel as PdependPackageModel;

class PdependMapper implements MapperInterface
{
    /**
     * @var ReflectionService
     */
    private $reflectionService;

    /**
     * @param ReflectionService $reflectionService
     */
    public function __construct(ReflectionService $reflectionService)
    {
        $this->reflectionService = $reflectionService;
    }

    /**
     * @inheritDoc
     */
    public function map(ModelInterface $pdependMetrics)
    {
        $classes = new ClassesModel();

        foreach ($pdependMetrics->getPackages() as $pdependPackage) {
            /* @var $pdependPackage PdependPackageModel */

            $namespaceName = $pdependPackage->getName();

            foreach ($pdependPackage->getClasses() as $pdependClass) {
                /* @var $pdependClass PdependClassModel */

                $className = $pdependClass->getName();
                $class = new ClassModel($className, $namespaceName);

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
                    $classSource = $this->reflectionService->getClassSource($file, $class->getFullQualifiedName());

                    $smell = new SmellModel('metrics', 'High overall complexity', $classSource, 1);
                    $class->addSmell($smell);
                }

                foreach ($pdependClass->getMethods() as $pdependMethod) {
                    /* @var $pdependMethod PdependMethodModel */

                    $methodName = $pdependMethod->getName();

                    $methodMetrics = $pdependMethod->getMetrics();

                    if ($methodMetrics['ccn'] > 7) {
                        $methodSource = $this->reflectionService->getMethodSource(
                            $file,
                            $class->getFullQualifiedName(),
                            $methodName
                        );

                        $smell = new SmellModel('metrics', 'Complex method ' . $methodName . '()', $methodSource, 1);
                        $class->addSmell($smell);
                    }
                }
            }
        }

        return $classes;
    }
}
