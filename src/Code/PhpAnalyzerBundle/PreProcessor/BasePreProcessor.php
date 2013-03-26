<?php

namespace Code\PhpAnalyzerBundle\PreProcessor;

use Code\AnalyzerBundle\PreProcessor\PreProcessorInterface;
use Code\AnalyzerBundle\Result\Result;
use Code\AnalyzerBundle\Result\Source\Source;
use Code\AnalyzerBundle\Result\Source\Storage\FilesystemStorage;
use Code\PhpAnalyzerBundle\Node\PhpClassNode;
use Code\PhpAnalyzerBundle\Node\PhpFileNode;
use Code\PhpAnalyzerBundle\ReflectionService;

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
