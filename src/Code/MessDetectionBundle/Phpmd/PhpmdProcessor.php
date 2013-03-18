<?php

namespace Code\MessDetectionBundle\Phpmd;

use Code\AnalyzerBundle\Analyzer\Processor\ProcessorInterface;
use Code\AnalyzerBundle\Model\NodeReference;
use Code\AnalyzerBundle\Model\SourceRange;
use Code\AnalyzerBundle\ReflectionService;
use Code\AnalyzerBundle\Model\ClassesModel;
use Code\AnalyzerBundle\Model\ClassModel;
use Code\AnalyzerBundle\Model\SmellModel;
use Code\AnalyzerBundle\ResultBuilder;

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
    public function process(ResultBuilder $resultBuilder, $filename)
    {
        $xml = simplexml_load_file($filename);

        //$pmdAttributes = $xml->attributes();
        //$pmdVersion = (string)$pmdAttributes['version'];
        //$pmdTimestamp = new \DateTime($pmdAttributes['timestamp']);

        foreach ($xml->file as $fileNode) {
            $fileAttributes = $fileNode->attributes();
            $fileName = (string)$fileAttributes['name'];

            //$className = $this->reflectionService->getClassNameForFile($fileName);
            //$namespaceName = $this->reflectionService->getNamespaceNameForFile($fileName);

            $fileResultNode = $resultBuilder->getNode($fileName);
            $classResultReference = current($resultBuilder->getIncomingReferences($fileResultNode));

            foreach ($fileNode->violation as $violationNode) {
                $violationAttributes = $violationNode->attributes();
                $violationBeginLine = (string)$violationAttributes['beginline'];
                $violationEndLine = (string)$violationAttributes['endline'];
                $violationRule = (string)$violationAttributes['rule'];
                //$violationRuleset = (string)$violationAttributes['ruleset'];
                //$violationExternalInfoUrl = (string)$violationAttributes['externalInfoUrl'];
                //$violationPriority = (string)$violationAttributes['priority'];

                $violationText = (string)$violationNode;

                $sourceRange = new SourceRange($violationBeginLine, $violationEndLine);

                $smell = new SmellModel($classResultReference, 'Mess', $violationRule, $violationText, $sourceRange, 1);
                $resultBuilder->addSmell($smell);
            }
        }
    }
}
