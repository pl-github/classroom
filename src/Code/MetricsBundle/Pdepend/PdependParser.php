<?php

namespace Code\MetricsBundle\Pdepend;

use Code\AnalyzerBundle\Analyzer\Parser\ParserInterface;
use Code\MetricsBundle\Pdepend\Model\ClassModel;
use Code\MetricsBundle\Pdepend\Model\MethodModel;
use Code\MetricsBundle\Pdepend\Model\MetricsModel;
use Code\MetricsBundle\Pdepend\Model\PackageModel;

class PdependParser implements ParserInterface
{
    /**
     * @inheritDoc
     */
    public function parse($filename)
    {
        $xml = simplexml_load_file($filename);

        $metricsAttributes = $xml->attributes();
        $metricsMetrics = array();
        foreach ($metricsAttributes as $metricsAttributeKey => $metricsAttributeValue) {
            $metricsMetrics[$metricsAttributeKey] = (string)$metricsAttributeValue;
        }
        $generated = new \DateTime($metricsMetrics['generated']);
        $pdepend = (string)$metricsMetrics['pdepend'];
        unset($metricsMetrics['generated'], $metricsMetrics['pdepend']);

        $metrics = new MetricsModel($generated, $pdepend, $metricsMetrics);

        foreach ($xml->package as $packageNode) {
            $packageAttributes = $packageNode->attributes();
            $packageMetrics = array();
            foreach ($packageAttributes as $packageAttributeKey => $packageAttributeValue) {
                $packageMetrics[$packageAttributeKey] = (string)$packageAttributeValue;
            }
            $packageName = $packageMetrics['name'];
            unset($packageMetrics['name']);

            $package = new PackageModel($packageName, $packageMetrics);

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

                $class = new ClassModel($className, $fileName, $classMetrics);

                $package->addClass($class);

                foreach ($classNode->method as $methodNode) {
                    $methodAttributes = $methodNode->attributes();
                    $methodMetrics = array();
                    foreach ($methodAttributes as $methodAttributeKey => $methodAttributeValue) {
                        $methodMetrics[$methodAttributeKey] = (string)$methodAttributeValue;
                    }
                    $methodName = $methodMetrics['name'];
                    unset($methodMetrics['name']);

                    $method = new MethodModel($methodName, $methodMetrics);

                    $class->addMethod($method);
                }
            }

            $metrics->addPackage($package);
        }

        return $metrics;
    }
}
