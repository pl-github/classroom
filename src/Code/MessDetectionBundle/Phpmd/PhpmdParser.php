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

        foreach ($xml->file as $fileNode)
        {
            $fileAttributes = $fileNode->attributes();
            $fileName = (string)$fileAttributes['name'];

            $file = new FileModel($fileName);

            foreach ($fileNode->violation as $violationNode)
            {
                $violationAttributes = $violationNode->attributes();
                $violationBeginLine = (integer)$violationAttributes['beginLine'];
                $violationEndLine = (integer)$violationAttributes['endLine'];
                $violationRule = (string)$violationAttributes['rule'];
                $violationRuleset = (string)$violationAttributes['ruleset'];
                $violationExternalInfoUrl = (string)$violationAttributes['externalInfoUrl'];
                $violationPriority = (integer)$violationAttributes['priority'];

                $violationText = (string)$violationNode;

                $violation = new ViolationModel(
                    $violationBeginLine,
                    $violationEndLine,
                    $violationRule,
                    $violationRuleset,
                    $violationExternalInfoUrl,
                    $violationPriority,
                    $violationText
                );

                $file->addViolation($violation);
            }

            $pmd->addFile($file);
        }

        return $pmd;
    }
}
