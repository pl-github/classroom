<?php

namespace Code\MessDetectionBundle\Phpmd;

use Code\MessDetectionBundle\Phpmd\Model\FileModel;
use Code\MessDetectionBundle\Phpmd\Model\PmdModel;
use Code\MessDetectionBundle\Phpmd\Model\ViolationModel;
use Code\ProjectBundle\ClassnameService;

class PhpmdParser
{
    /**
     * @var ClassnameService
     */
    private $classnameService;

    /**
     * @param ClassnameService $classnameService
     */
    public function __construct(ClassnameService $classnameService)
    {
        $this->classnameService = $classnameService;
    }

    /**
     * Parse phpmd file
     *
     * @param string $filename
     * @return PmdModel
     */
    public function parse($filename)
    {
        $xml = simplexml_load_file($filename);

        $pmdAttributes = $xml->attributes();
        $pmdName = (string)$pmdAttributes['name'];
        $pmdTimestamp = new \DateTime($pmdAttributes['timestamp']);

        $pmd = new PmdModel($pmdName, $pmdTimestamp);

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
