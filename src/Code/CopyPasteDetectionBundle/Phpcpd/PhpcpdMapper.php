<?php

namespace Code\CopyPasteDetectionBundle\Phpcpd;

use Code\AnalyzerBundle\Analyzer\Mapper\MapperInterface;
use Code\AnalyzerBundle\Analyzer\Model\ModelInterface;
use Code\AnalyzerBundle\ClassnameService;
use Code\AnalyzerBundle\Model\ClassesModel;
use Code\AnalyzerBundle\Model\ClassModel;
use Code\AnalyzerBundle\Model\MetricModel;
use Code\AnalyzerBundle\Model\SmellModel;
use Code\CopyPasteDetectionBundle\Phpcpd\Model\DuplicationModel;
use Code\CopyPasteDetectionBundle\Phpcpd\Model\FileModel;
use Code\CopyPasteDetectionBundle\Phpcpd\Model\PmdCpdModel;

class PhpcpdMapper implements MapperInterface
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
    public function map(ModelInterface $pmdCpd)
    {
        $classes = new ClassesModel();

        foreach ($pmdCpd->getDuplications() as $duplication) {
            /* @var $duplication DuplicationModel */

            $files = $duplication->getFiles();

            foreach ($files as $file) {
                /* @var $file FileModel */
                $classname = $this->classnameService->getClassnameForFile($file->getPath());

                $class = new ClassModel($classname);

                $class->addMetric(new MetricModel('duplication', $duplication->getLines()));

                $smell = new SmellModel('copy_paste_detection', 'Similar code', $duplication->getCodefragment(), 1);
                $class->addSmell($smell);

                $classes->addClass($class);
            }
        }

        return $classes;
    }
}
