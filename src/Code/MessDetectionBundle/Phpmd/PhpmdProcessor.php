<?php

namespace Code\MessDetectionBundle\Phpmd;

use Code\AnalyzerBundle\Analyzer\Processor\ProcessorInterface;
use Code\AnalyzerBundle\Model\SourceModel;
use Code\AnalyzerBundle\ReflectionService;
use Code\AnalyzerBundle\Model\ClassesModel;
use Code\AnalyzerBundle\Model\ClassModel;
use Code\AnalyzerBundle\Model\MetricModel;
use Code\AnalyzerBundle\Model\SmellModel;

class PhpmdProcessor implements ProcessorInterface
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

        //$pmdAttributes = $xml->attributes();
        //$pmdVersion = (string)$pmdAttributes['version'];
        //$pmdTimestamp = new \DateTime($pmdAttributes['timestamp']);

        foreach ($xml->file as $fileNode) {
            $fileAttributes = $fileNode->attributes();
            $fileName = (string)$fileAttributes['name'];

            $className = $this->reflectionService->getClassNameForFile($fileName);
            $namespaceName = $this->reflectionService->getNamespaceNameForFile($fileName);

            $class = new ClassModel($className, $namespaceName);
            $classes->addClass($class);

            foreach ($fileNode->violation as $violationNode) {
                $violationAttributes = $violationNode->attributes();
                $violationBeginLine = (string)$violationAttributes['beginline'];
                $violationEndLine = (string)$violationAttributes['endline'];
                $violationRule = (string)$violationAttributes['rule'];
                //$violationRuleset = (string)$violationAttributes['ruleset'];
                //$violationExternalInfoUrl = (string)$violationAttributes['externalInfoUrl'];
                //$violationPriority = (string)$violationAttributes['priority'];

                $violationText = (string)$violationNode;

                $sourceLines = $this->reflectionService->getSourceLines($fileName);
                $source = new SourceModel($sourceLines, $violationBeginLine, $violationEndLine, 5);

                $smell = new SmellModel('Mess', $violationRule, $violationText, $source, 1);
                $class->addSmell($smell);
            }
        }

        return $classes;
    }
}
