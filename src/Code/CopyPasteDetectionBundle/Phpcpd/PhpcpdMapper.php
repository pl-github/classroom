<?php

namespace Code\CopyPasteDetectionBundle\Phpcpd;

use Code\CopyPasteDetectionBundle\Phpcpd\Model\DuplicationModel;
use Code\CopyPasteDetectionBundle\Phpcpd\Model\FileModel;
use Code\CopyPasteDetectionBundle\Phpcpd\Model\PmdCpdModel;
use Code\ProjectBundle\ClassnameService;
use Code\ProjectBundle\Model\ClassesModel;
use Code\ProjectBundle\Model\ClassModel;
use Code\ProjectBundle\Model\SmellModel;

class PhpcpdMapper
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
     * Map phpcpd models to class models
     * @param $duplications
     * @return ResultCollection
     */
    public function map(PmdCpdModel $pmdCpd)
    {
        $classes = new ClassesModel();

        foreach ($pmdCpd->getDuplications() as $duplication) {
            /* @var $duplication DuplicationModel */
            $files = $duplication->getFiles();
            foreach ($files as $file) {
                /* @var $file FileModel */
                $classname = $this->classnameService->getClassnameForFile($file->getPath());

                $class = new ClassModel($classname);

                $smell = new SmellModel('copy_paste_detection', 'Similar code', $duplication->getCodefragment(), 1);
                $class->addSmell($smell);

                $classes->addClass($class);
            }
        }

        return $classes;
    }
}
