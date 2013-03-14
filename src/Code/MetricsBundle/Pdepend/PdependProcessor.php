<?php

namespace Code\MetricsBundle\Pdepend;

use Code\AnalyzerBundle\Analyzer\Processor\ProcessorInterface;
use Code\AnalyzerBundle\ReflectionService;
use Code\AnalyzerBundle\Model\ClassesModel;
use Code\AnalyzerBundle\Model\ClassModel;
use Code\AnalyzerBundle\Model\MetricModel;
use Code\AnalyzerBundle\Model\SmellModel;

class PdependProcessor implements ProcessorInterface
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
    public function process($filename)
    {
        $classes = new ClassesModel();

        $xml = simplexml_load_file($filename);

        $metricsAttributes = $xml->attributes();
        $metricsMetrics = array();
        foreach ($metricsAttributes as $metricsAttributeKey => $metricsAttributeValue) {
            $metricsMetrics[$metricsAttributeKey] = (string)$metricsAttributeValue;
        }
        $generated = new \DateTime($metricsMetrics['generated']);
        $pdepend = (string)$metricsMetrics['pdepend'];
        unset($metricsMetrics['generated'], $metricsMetrics['pdepend']);

        foreach ($xml->package as $packageNode) {
            $packageAttributes = $packageNode->attributes();
            $packageMetrics = array();
            foreach ($packageAttributes as $packageAttributeKey => $packageAttributeValue) {
                $packageMetrics[$packageAttributeKey] = (string)$packageAttributeValue;
            }
            $packageName = $packageMetrics['name'];
            unset($packageMetrics['name']);

            foreach ($packageNode->class as $classNode) {
                $classAttributes = $classNode->attributes();
                $classMetrics = array();
                foreach ($classAttributes as $classAttributeKey => $classAttributeValue) {
                    $classMetrics[$classAttributeKey] = (string)$classAttributeValue;
                }
                $className = $classMetrics['name'];
                unset($classMetrics['name']);

                $fileAttributes = $classNode->file->attributes();
                $fileName = (string)$fileAttributes['name'];

                $class = new ClassModel($className, $packageName);
                $classes->addClass($class);

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

                if ($classMetrics['wmc'] > 7) {
                    $classSource = $this->reflectionService->getClassSource($fileName, $class->getFullQualifiedName());

                    $smell = new SmellModel('metrics', 'High overall complexity', $classSource, 1);
                    $class->addSmell($smell);
                }

                /*
                foreach ($classNode->method as $methodNode) {
                    $methodAttributes = $methodNode->attributes();
                    $methodMetrics = array();
                    foreach ($methodAttributes as $methodAttributeKey => $methodAttributeValue) {
                        $methodMetrics[$methodAttributeKey] = (string)$methodAttributeValue;
                    }
                    $methodName = $methodMetrics['name'];
                    unset($methodMetrics['name']);
                }
                */
            }
        }

        return $classes;
    }
}
