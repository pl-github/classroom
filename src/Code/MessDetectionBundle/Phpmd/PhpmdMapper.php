<?php

namespace Code\MessDetectionBundle\Phpmd;

use Code\MessDetectionBundle\Phpmd\Model\FileModel;
use Code\MessDetectionBundle\Phpmd\Model\PmdModel;
use Code\MessDetectionBundle\Phpmd\Model\ViolationModel;
use Code\ProjectBundle\Model\ClassesModel;
use Code\ProjectBundle\Model\ClassModel;
use Code\ProjectBundle\Model\SmellModel;
use Code\ProjectBundle\ClassnameService;

class PhpmdMapper
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
     * Map pmd models to class models
     *
     * @param PmdModel $pmd
     * @return ClassesModel
     */
    public function map(PmdModel $pmd)
    {
        $classes = new ClassesModel();

        foreach ($pmd->getFiles() as $file) {
            /* @var $file FileModel */

            $fileName = $file->getName();
            $className = $this->classnameService->getClassnameForFile($fileName);

            foreach ($file->getViolations() as $violation) {
                /* @var $violation ViolationModel */

                $class = new ClassModel($className);

                $smell = new SmellModel('mess_detection', $violation->getText(), '', 1);
                $class->addSmell($smell);

                $classes->addClass($class);
            }
        }

        return $classes;
    }
}
