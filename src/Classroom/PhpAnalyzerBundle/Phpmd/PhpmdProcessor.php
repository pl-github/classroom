<?php

namespace Classroom\PhpAnalyzerBundle\Phpmd;

use Classroom\AnalyzerBundle\Processor\ProcessorInterface;
use Classroom\AnalyzerBundle\ReflectionService;
use Classroom\AnalyzerBundle\Result\Result;
use Classroom\AnalyzerBundle\Result\Smell\Smell;
use Classroom\AnalyzerBundle\Result\Source\SourceRange;
use Classroom\PhpAnalyzerBundle\Node\PhpFileNode;

class PhpmdProcessor implements ProcessorInterface
{
    /**
     * @var PhpmdCollector
     */
    private $collector;

    /**
     * @param PhpmdCollector $collector
     */
    public function __construct(PhpmdCollector $collector)
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

        //$pmdAttributes = $xml->attributes();
        //$pmdVersion = (string)$pmdAttributes['version'];
        //$pmdTimestamp = new \DateTime($pmdAttributes['timestamp']);

        foreach ($xml->file as $xmlFileNode) {
            $fileAttributes = $xmlFileNode->attributes();
            $fileName = (string)$fileAttributes['name'];

            $fileNode = $result->getNode(new PhpFileNode($fileName));
            $classNode = $result->getNode(current($result->getReference('node', 'children', $fileNode)));

            foreach ($xmlFileNode->violation as $xmlViolationNode) {
                $violationAttributes = $xmlViolationNode->attributes();
                $violationBeginLine = (string)$violationAttributes['beginline'];
                $violationEndLine = (string)$violationAttributes['endline'];
                $violationRule = (string)$violationAttributes['rule'];
                //$violationRuleset = (string)$violationAttributes['ruleset'];
                //$violationExternalInfoUrl = (string)$violationAttributes['externalInfoUrl'];
                $violationPriority = (string)$violationAttributes['priority'];

                $violationText = (string)$xmlViolationNode;

                $sourceRange = new SourceRange($violationBeginLine, $violationEndLine);

                $smell = new Smell('Mess', $violationRule, $violationText, $sourceRange, $violationPriority);
                $result->addSmell($smell, $classNode);
            }
        }

        $result->addArtifact($filename);
    }
}
