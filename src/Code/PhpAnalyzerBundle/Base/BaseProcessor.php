<?php

namespace Code\PhpAnalyzerBundle\Base;

use Code\AnalyzerBundle\Analyzer\Processor\ProcessorInterface;
use Code\AnalyzerBundle\Model\ResultModel;
use Code\AnalyzerBundle\Source\Source;
use Code\AnalyzerBundle\Source\Storage\FilesystemStorage;
use Code\PhpAnalyzerBundle\Node\PhpClassNode;
use Code\PhpAnalyzerBundle\Node\PhpFileNode;
use Code\PhpAnalyzerBundle\ReflectionService;

class BaseProcessor implements ProcessorInterface
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
    public function process(ResultModel $result, $files)
    {
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
    }
}
