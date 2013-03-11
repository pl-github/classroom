<?php

namespace Code\MessDetectionBundle\Phpmd;

use Code\AnalyzerBundle\Analyzer\Mapper\MapperInterface;
use Code\AnalyzerBundle\Analyzer\Model\ModelInterface;
use Code\AnalyzerBundle\ClassnameService;
use Code\AnalyzerBundle\Model\ClassesModel;
use Code\AnalyzerBundle\Model\ClassModel;
use Code\AnalyzerBundle\Model\SmellModel;
use Code\MessDetectionBundle\Phpmd\Model\FileModel;
use Code\MessDetectionBundle\Phpmd\Model\PmdModel;
use Code\MessDetectionBundle\Phpmd\Model\ViolationModel;

class PhpmdMapper implements MapperInterface
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
     * @inheritDoc
     */
    public function map(ModelInterface $pmd)
    {
        $classes = new ClassesModel();

        foreach ($pmd->getFiles() as $file) {
            /* @var $file FileModel */

            $fileName = $file->getName();
            $className = $this->classnameService->getClassnameForFile($fileName);

            $class = new ClassModel($className);

            foreach ($file->getViolations() as $violation) {
                /* @var $violation ViolationModel */

                $smell = new SmellModel('mess_detection', $violation->getText(), '', 1);
                $class->addSmell($smell);
            }

            $classes->addClass($class);
        }

        return $classes;
    }
}
