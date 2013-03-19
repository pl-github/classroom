<?php

namespace Code\PhpAnalyzerBundle\Base;

use Code\AnalyzerBundle\Analyzer\Processor\ProcessorInterface;
use Code\AnalyzerBundle\Model\ResultModel;
use Code\AnalyzerBundle\ReflectionService;
use Code\AnalyzerBundle\Source\Source;
use Code\AnalyzerBundle\Source\Storage\FilesystemStorage;
use Code\PhpAnalyzerBundle\Node\PhpClassNode;
use Code\PhpAnalyzerBundle\Node\PhpFileNode;

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

            $className = $this->reflectionService->getClassNameForFile($filename);
            $classNode = new PhpClassNode($className);

            $filesystemStorage = new FilesystemStorage($filename);
            $source = new Source($filesystemStorage);

            $result
                ->addNode($fileNode)
                ->addNode($classNode, $fileNode)
                ->addSource($source, $fileNode);
        }

    }
}
