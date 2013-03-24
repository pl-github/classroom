<?php

namespace Code\ProjectBundle;

use Code\AnalyzerBundle\ResultBuilderInterface;
use Code\AnalyzerBundle\Writer\WriterInterface;
use Code\ProjectBundle\Entity\Revision;
use Code\ProjectBundle\Entity\Project;
use Code\RepositoryBundle\RepositoryFactory;

class Builder
{
    /**
     * @var ResultBuilderInterface
     */
    private $resultBuilder;

    /**
     * @var WriterInterface
     */
    private $writer;

    /**
     * @var RepositoryFactory
     */
    private $repositoryFactory;

    /**
     * @var DataDirFactory
     */
    private $dataDirFactory;

    /**
     * @param ResultBuilderInterface $resultBuilder
     * @param WriterInterface        $writer
     * @param RepositoryFactory      $repositoryFactory
     * @param DataDirFactory         $dataDirFactory
     */
    public function __construct(ResultBuilderInterface $resultBuilder,
                                WriterInterface $writer,
                                RepositoryFactory $repositoryFactory,
                                DataDirFactory $dataDirFactory)
    {
        $this->resultBuilder = $resultBuilder;
        $this->writer = $writer;
        $this->repositoryFactory = $repositoryFactory;
        $this->dataDirFactory = $dataDirFactory;
    }

    /**
     * Build revision
     *
     * @param Revision $revision
     */
    public function build(Revision $revision)
    {
        $project = $revision->getProject();

        $dataDir = $this->dataDirFactory->factory($project);

        $repositoryConfig = $project->getRepositoryConfig();
        $repository = $this->repositoryFactory->factory($repositoryConfig, $dataDir);

        $sourceDirectory = $repository->getSourceDirectory();

        $version = $repository->determineVersion();

        $revision->setRevision($version);

        $tsStart = microtime(true);

        $result = $this->resultBuilder->build($sourceDirectory, $dataDir->getWorkingDirectory());
        $resultFilename = $this->writer->write($result, $dataDir->getBuildsDirectory() . '/' . $version . '.phar');

        $tsEnd = microtime(true);
        $runTime = $tsEnd - $tsStart;

        $revision
            ->setStatus(2)
            ->setRevision($version)
            ->setResultFilename(basename($resultFilename))
            ->setGpa($result->getGpa())
            ->setRunTime($runTime)
            ->setBuiltAt($result->getBuiltAt());
    }
}
