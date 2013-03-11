<?php

namespace Code\CopyPasteDetectionBundle\Phpcpd;

use Code\AnalyzerBundle\Analyzer\Mapper\MapperInterface;
use Code\AnalyzerBundle\Analyzer\Model\ModelInterface;
use Code\AnalyzerBundle\Model\ClassesModel;
use Code\AnalyzerBundle\Model\ClassModel;
use Code\AnalyzerBundle\Model\MetricModel;
use Code\AnalyzerBundle\Model\SmellModel;
use Code\AnalyzerBundle\ReflectionService;
use Code\CopyPasteDetectionBundle\Phpcpd\Model\DuplicationModel;
use Code\CopyPasteDetectionBundle\Phpcpd\Model\FileModel;
use Code\CopyPasteDetectionBundle\Phpcpd\Model\PmdCpdModel;

class PhpcpdMapper implements MapperInterface
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
    public function map(ModelInterface $pmdCpd)
    {
        $classes = new ClassesModel();

        foreach ($pmdCpd->getDuplications() as $duplication) {
            /* @var $duplication DuplicationModel */

            $files = $duplication->getFiles();

            foreach ($files as $file) {
                /* @var $file FileModel */

                $fileName = $file->getPath();
                $className = $this->reflectionService->getClassNameForFile($fileName);
                $namespaceName = $this->reflectionService->getNamespaceNameForFile($fileName);

                $class = new ClassModel($className, $namespaceName);

                $class->addMetric(new MetricModel('duplication', $duplication->getLines()));

                $smell = new SmellModel('copy_paste_detection', 'Similar code', $duplication->getCodefragment(), 1);
                $class->addSmell($smell);

                $classes->addClass($class);
            }
        }

        return $classes;
    }
}
