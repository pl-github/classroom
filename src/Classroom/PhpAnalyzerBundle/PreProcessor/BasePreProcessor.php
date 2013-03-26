<?php

namespace Classroom\PhpAnalyzerBundle\PreProcessor;

use Classroom\AnalyzerBundle\PreProcessor\PreProcessorInterface;
use Classroom\AnalyzerBundle\Result\Result;
use Classroom\AnalyzerBundle\Result\Source\Source;
use Classroom\AnalyzerBundle\Result\Source\Storage\FilesystemStorage;
use Classroom\PhpAnalyzerBundle\Node\PhpClassNode;
use Classroom\PhpAnalyzerBundle\Node\PhpFileNode;
use Classroom\PhpAnalyzerBundle\ReflectionService;

class BasePreProcessor implements PreProcessorInterface
{
    /**
     * @var BaseCollector
     */
    private $collector;

    /**
     * @var ReflectionService
     */
    private $reflectionService;

    /**
     * @param BaseCollector     $collector
     * @param ReflectionService $reflectionService
     */
    public function __construct(BaseCollector $collector, ReflectionService $reflectionService)
    {
        $this->collector = $collector;
        $this->reflectionService = $reflectionService;
    }

    /**
     * @inheritDoc
     */
    public function process(Result $result)
    {
        $files = $this->collector->collect(
            $result->getLog(),
            $result->getSourceDirectory(),
            $result->getWorkingDirectory()
        );

        foreach ($files as $filename) {
            $fileNode = new PhpFileNode($filename);
            $result->addNode($fileNode);

            $className = $this->reflectionService->getClassNameForFile($filename);
            $classNode = new PhpClassNode($className);
            $result->addNode($classNode, $fileNode);

            $filesystemStorage = new FilesystemStorage($filename);
            $source = new Source($filesystemStorage);

            $result->addSource($source, $fileNode);
        }

        $result->getLog()->addProcess('(BasePreProcessor) Added ' . count($files) . ' files.');
    }
}
