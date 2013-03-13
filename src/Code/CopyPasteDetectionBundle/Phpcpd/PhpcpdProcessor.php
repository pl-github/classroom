<?php

namespace Code\CopyPasteDetectionBundle\Phpcpd;

use Code\AnalyzerBundle\Analyzer\Processor\ProcessorInterface;
use Code\AnalyzerBundle\ReflectionService;
use Code\AnalyzerBundle\Model\ClassesModel;
use Code\AnalyzerBundle\Model\ClassModel;
use Code\AnalyzerBundle\Model\MetricModel;
use Code\AnalyzerBundle\Model\SmellModel;

class PhpcpdProcessor implements ProcessorInterface
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
        $xml = simplexml_load_file($filename);

        $classes = new ClassesModel();
        foreach ($xml->duplication as $duplicationNode) {
            $duplicationAttributes = $duplicationNode->attributes();

            $lines = (string)$duplicationAttributes['lines'];
            //$tokens = (string)$duplicationAttributes['tokens'];

            $codefragment = (string)$duplicationNode->codefragment;

            foreach ($duplicationNode->file as $fileNode) {
                $fileAttributes = $fileNode->attributes();

                $path = (string)$fileAttributes['path'];
                //$line = (string)$fileAttributes['line'];

                $className = $this->reflectionService->getClassNameForFile($path);
                $namespaceName = $this->reflectionService->getNamespaceNameForFile($path);

                $class = new ClassModel($className, $namespaceName);
                $classes->addClass($class);

                $metric = new MetricModel('duplication', $lines);
                $class->addMetric($metric);

                $smell = new SmellModel('copy_paste_detection', 'Similar code', $codefragment, 1);
                $class->addSmell($smell);
            }
        }

        return $classes;
    }
}
