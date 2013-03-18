<?php

namespace Code\PhpAnalyzerBundle;

use Code\AnalyzerBundle\Analyzer\AnalyzerInterface;
use Code\AnalyzerBundle\Model\ResultModel;
use Code\AnalyzerBundle\Model\SmellModel;
use Code\AnalyzerBundle\Node\NodeInterface;
use Code\AnalyzerBundle\Node\NodeReference;
use Code\AnalyzerBundle\ReflectionService;
use Code\AnalyzerBundle\ResultBuilderInterface;
use Code\PhpAnalyzerBundle\Node\PhpClassNode;
use Code\PhpAnalyzerBundle\Node\PhpFileNode;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class ResultBuilder implements ResultBuilderInterface
{
    /**
     * @var ReflectionService
     */
    private $reflectionService;

    /**
     * @var AnalyzerInterface
     */
    private $analyzer;

    /**
     * @var ResultModel
     */
    private $result;

    /**
     * @param ReflectionService $reflectionService
     * @param AnalyzerInterface $analyzer
     */
    public function __construct(ReflectionService $reflectionService, AnalyzerInterface $analyzer)
    {
        $this->reflectionService = $reflectionService;
        $this->analyzer = $analyzer;

        $this->result = new ResultModel();
    }

    /**
     * @param $sourceDirectory
     */
    public function createResult($sourceDirectory, $workDirectory)
    {
        $finder = new Finder();
        $finder->name('*.php');

        foreach ($finder->in($sourceDirectory) as $file)
        {
            /* @var $file SplFileInfo */

            //echo $file->getPathname().PHP_EOL;

            $fileNode = new PhpFileNode($file->getPathname());
            $fileNode->setSourceFilename($file->getPathname());
            $this->result->addNode($fileNode);

            $fileReference = new NodeReference($fileNode);

            $className = $this->reflectionService->getClassNameForFile($file->getPathname());
            $namespaceName = $this->reflectionService->getNamespaceNameForFile($file->getPathname());

            $classNode = new PhpClassNode($className, $namespaceName, $fileReference);
            $this->result->addNode($classNode);
        }

        $this->analyzer->analyze($this, $sourceDirectory, $workDirectory);

        return $this->result;
    }

    /**
     * Add node
     *
     * @param NodeInterface $node
     * @return $this
     */
    public function addNode(NodeInterface $node)
    {
        $this->result->addNode($node);

        return $this;
    }

    /**
     * Return node
     *
     * @param string $fullQualifiedName
     * @return NodeInterface
     */
    public function getNode($fullQualifiedName)
    {
        return $this->result->getNode($fullQualifiedName);
    }

    public function getNodes()
    {
        return $this->result->getNodes();
    }

    public function getIncomingReferences($fullQualifiedName)
    {
        return $this->result->getIncomingReferences($fullQualifiedName);
    }

    public function getOutgoingReference($fullQualifiedName)
    {
        return $this->result->getOutgoingReference($fullQualifiedName);
    }

    /**
     * Add smell
     *
     * @param SmellModel $smell
     * @return $this
     */
    public function addSmell(SmellModel $smell)
    {
        $this->result->addSmell($smell);

        return $this;
    }
}