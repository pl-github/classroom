<?php

namespace Code\PhpAnalyzerBundle\Phpmd;

use Code\AnalyzerBundle\Analyzer\Processor\ProcessorInterface;
use Code\AnalyzerBundle\ReflectionService;
use Code\AnalyzerBundle\Smell\Smell;
use Code\AnalyzerBundle\Source\SourceRange;
use Code\AnalyzerBundle\Model\ResultModel;

class PhpmdProcessor implements ProcessorInterface
{
    /**
     * @inheritDoc
     */
    public function process(ResultModel $result, $filename)
    {
        if (!file_exists($filename)) {
            throw new \Exception('phpmd report xml file not found.');
        }

        $xml = simplexml_load_file($filename);

        //$pmdAttributes = $xml->attributes();
        //$pmdVersion = (string)$pmdAttributes['version'];
        //$pmdTimestamp = new \DateTime($pmdAttributes['timestamp']);

        foreach ($xml->file as $xmlFileNode) {
            $fileAttributes = $xmlFileNode->attributes();
            $fileName = (string)$fileAttributes['name'];

            $fileNode = $result->getNode($fileName);
            $classNode = $result->getNode(current($result->getIncoming('node', $fileNode)));

            foreach ($xmlFileNode->violation as $xmlViolationNode) {
                $violationAttributes = $xmlViolationNode->attributes();
                $violationBeginLine = (string)$violationAttributes['beginline'];
                $violationEndLine = (string)$violationAttributes['endline'];
                $violationRule = (string)$violationAttributes['rule'];
                //$violationRuleset = (string)$violationAttributes['ruleset'];
                //$violationExternalInfoUrl = (string)$violationAttributes['externalInfoUrl'];
                //$violationPriority = (string)$violationAttributes['priority'];

                $violationText = (string)$xmlViolationNode;

                $sourceRange = new SourceRange($violationBeginLine, $violationEndLine);

                $smell = new Smell('Mess', $violationRule, $violationText, $sourceRange, 1);
                $result->addSmell($smell, $classNode);
            }
        }
    }
}
