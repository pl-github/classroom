<?php

namespace Classroom\ProjectBundle;

use Classroom\AnalyzerBundle\ResultBuilderInterface;
use Classroom\AnalyzerBundle\Writer\WriterInterface;
use Classroom\ProjectBundle\Config\ConfigParser;
use Classroom\ProjectBundle\Entity\Revision;
use Classroom\ProjectBundle\Entity\Project;
use Classroom\RepositoryBundle\RepositoryFactory;

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
     * @param callable $logCallback
     */
    public function build(Revision $revision, callable $logCallback = null)
    {
        $project = $revision->getProject();

        $dataDir = $this->dataDirFactory->factory($project);

        $repositoryConfig = $project->getRepositoryConfig();
        $repository = $this->repositoryFactory->factory($repositoryConfig, $dataDir);

        $sourceDirectory = $repository->getSourceDirectory();

        $configParser = new ConfigParser();
        $config = $configParser->parse($sourceDirectory . '/.classroom.yml');

        $version = $repository->determineVersion();
        $targetDirectory = $repository->getSourceDirectory() . $config->getTarget();

        if (!file_exists($targetDirectory)) {
            throw new \Exception('Target directory ' . $targetDirectory . ' does not exist.');
        }

        $revision->setRevision($version);

        $tsStart = microtime(true);

        $result = $this->resultBuilder->buildResult($targetDirectory, $dataDir->getWorkingDirectory(), $logCallback);
        $resultFilename = $this->writer->write($result, $dataDir->getBuildsDirectory() . '/' . $version . '.phar');

        $tsEnd = microtime(true);
        $runTime = $tsEnd - $tsStart;

        $revision
            ->setStatus(2)
            ->setRevision($version)
            ->setResultFilename(basename($resultFilename))
            ->setGpa($result->getGpa())
            ->setBreakdown($result->getBreakdown())
            //->setHotspots($result->getHotspots())
            ->setRunTime($runTime)
            ->setBuiltAt($result->getBuiltAt());

        return $resultFilename;
    }
}
