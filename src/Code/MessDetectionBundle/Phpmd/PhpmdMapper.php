<?php

namespace Code\MessDetectionBundle\Phpmd;

use Code\AnalyzerBundle\Analyzer\Mapper\MapperInterface;
use Code\AnalyzerBundle\Analyzer\Model\ModelInterface;
use Code\AnalyzerBundle\Model\ClassesModel;
use Code\AnalyzerBundle\Model\ClassModel;
use Code\AnalyzerBundle\Model\SmellModel;
use Code\AnalyzerBundle\ReflectionService;
use Code\MessDetectionBundle\Phpmd\Model\FileModel;
use Code\MessDetectionBundle\Phpmd\Model\PmdModel;
use Code\MessDetectionBundle\Phpmd\Model\ViolationModel;

class PhpmdMapper implements MapperInterface
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
    public function map(ModelInterface $pmd)
    {
        $classes = new ClassesModel();

        foreach ($pmd->getFiles() as $file) {
            /* @var $file FileModel */

            $fileName = $file->getName();
            $className = $this->reflectionService->getClassNameForFile($fileName);
            $namespaceName = $this->reflectionService->getNamespaceNameForFile($fileName);

            $class = new ClassModel($className, $namespaceName);

            foreach ($file->getViolations() as $violation) {
                /* @var $violation ViolationModel */

                $source = $this->reflectionService->getSourceExtract(
                    $fileName,
                    $violation->getBeginLine(),
                    $violation->getEndLine()
                );

                $smell = new SmellModel('mess_detection', $violation->getText(), $source, 1);
                $class->addSmell($smell);
            }

            $classes->addClass($class);
        }

        return $classes;
    }
}
