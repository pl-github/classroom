<?php

namespace Code\MessDetectionBundle\Phpmd;

use Code\AnalyzerBundle\Analyzer\Parser\ParserInterface;
use Code\MessDetectionBundle\Phpmd\Model\FileModel;
use Code\MessDetectionBundle\Phpmd\Model\PmdModel;
use Code\MessDetectionBundle\Phpmd\Model\ViolationModel;

class PhpmdParser implements ParserInterface
{
    /**
     * @inheritDoc
     */
    public function parse($filename)
    {
        $xml = simplexml_load_file($filename);

        $pmdAttributes = $xml->attributes();
        $pmdVersion = (string)$pmdAttributes['version'];
        $pmdTimestamp = new \DateTime($pmdAttributes['timestamp']);

        $pmd = new PmdModel($pmdVersion, $pmdTimestamp);

        foreach ($xml->file as $fileNode) {
            $fileAttributes = $fileNode->attributes();
            $fileName = (string)$fileAttributes['name'];

            $file = new FileModel($fileName);

            foreach ($fileNode->violation as $violationNode) {
                $violationAttributes = $violationNode->attributes();
                $violationBeginLine = (string)$violationAttributes['beginline'];
                $violationEndLine = (string)$violationAttributes['endline'];
                $violationRule = (string)$violationAttributes['rule'];
                $violationRuleset = (string)$violationAttributes['ruleset'];
                $violationExternalInfoUrl = (string)$violationAttributes['externalInfoUrl'];
                $violationPriority = (string)$violationAttributes['priority'];

                $violationText = (string)$violationNode;

                $violation = new ViolationModel(
                    (integer)$violationBeginLine,
                    (integer)$violationEndLine,
                    $violationRule,
                    $violationRuleset,
                    $violationExternalInfoUrl,
                    (integer)$violationPriority,
                    trim($violationText)
                );

                $file->addViolation($violation);
            }

            $pmd->addFile($file);
        }

        return $pmd;
    }
}
