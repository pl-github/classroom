<?php

namespace Code\PhpAnalyzerBundle\Pdepend;

use Code\AnalyzerBundle\Processor\ProcessorInterface;
use Code\AnalyzerBundle\Result\Metric\Metric;
use Code\AnalyzerBundle\Result\Result;
use Code\AnalyzerBundle\ReflectionService;

class PdependProcessor implements ProcessorInterface
{
    /**
     * @var PdependCollector
     */
    private $collector;

    /**
     * @param PdependCollector $collector
     */
    public function __construct(PdependCollector $collector)
    {
        $this->collector = $collector;
    }

    /**
     * @inheritDoc
     */
    public function process(Result $result)
    {
        $filename = $this->collector->collect(
            $result->getLog(),
            $result->getSourceDirectory(),
            $result->getWorkingDirectory()
        );

        $xml = simplexml_load_file($filename);

        $metricsAttributes = $xml->attributes();
        $metricsMetrics = array();
        foreach ($metricsAttributes as $metricKey => $metricValue) {
            $metricsMetrics[$metricKey] = (string)$metricValue;
        }
        //$generated = new \DateTime($metricsMetrics['generated']);
        //$pdepend = (string)$metricsMetrics['pdepend'];
        unset($metricsMetrics['generated'], $metricsMetrics['pdepend']);

        foreach ($xml->package as $packageNode) {
            $packageAttributes = $packageNode->attributes();
            $packageMetrics = array();
            foreach ($packageAttributes as $packageKey => $packageValue) {
                $packageMetrics[$packageKey] = (string)$packageValue;
            }
            $packageName = $packageMetrics['name'];
            unset($packageMetrics['name']);

            foreach ($packageNode->class as $classNode) {
                $classAttributes = $classNode->attributes();
                $classMetrics = array();
                foreach ($classAttributes as $classKey => $classValue) {
                    $classMetrics[$classKey] = (string)$classValue;
                }
                $className = $classMetrics['name'];
                unset($classMetrics['name']);

                $fileAttributes = $classNode->file->attributes();
                //$fileName = (string)$fileAttributes['name'];

                $classResultNode = $result->getNode($packageName . '\\' . $className);

                $lines = $classMetrics['loc'];
                $linesOfCode = $classMetrics['eloc'];
                $methods = $classMetrics['nom'];
                $linesOfCodePerMethod = $methods ? $linesOfCode / $methods : 0;
                $complexity = $classMetrics['wmcnp'];
                $complexityPerMethod = $methods ? $complexity / $methods : 0;

                $classResultNode->addMetric(new Metric('lines', $lines));
                $classResultNode->addMetric(new Metric('linesOfCode', $linesOfCode));

                $classResultNode->addMetric(new Metric('methods', $methods));
                $classResultNode->addMetric(new Metric('linesOfCodePerMethod', $linesOfCodePerMethod));

                $classResultNode->addMetric(new Metric('complexity', $complexity));
                $classResultNode->addMetric(new Metric('complexityPerMethod', $complexityPerMethod));
            }
        }

        $result->addArtifact($filename);
    }
}
