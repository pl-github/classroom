<?php

namespace Code\PhpAnalyzerBundle\Phpmd;

use Code\AnalyzerBundle\Analyzer\Processor\ProcessorInterface;
use Code\AnalyzerBundle\Model\SourceRange;
use Code\AnalyzerBundle\Model\SmellModel;
use Code\AnalyzerBundle\ReflectionService;
use Code\AnalyzerBundle\ResultBuilderInterface;

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
    public function process(ResultBuilderInterface $resultBuilder, $filename)
    {
        if (!file_exists($filename)) {
            throw new \Exception('phpmd report xml file not found.');
        }

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
