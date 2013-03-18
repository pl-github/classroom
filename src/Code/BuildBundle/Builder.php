<?php

namespace Code\BuildBundle;

use Code\AnalyzerBundle\Analyzer\AnalyzerInterface;
use Code\BuildBundle\Entity\Build;
use Code\ProjectBundle\Entity\Project;
use Code\RepositoryBundle\RepositoryFactory;

class Builder
{
    /**
     * @var AnalyzerInterface
     */
    private $analyzer;

    /**
     * @var RepositoryFactory
     */
    private $repositoryFactory;

    /**
     * @var string
     */
    private $rootDir;

    /**
     * @param AnalyzerInterface $analyzer
     * @param RepositoryFactory $repositoryFactory
     * @param string            $rootDir
     */
    public function __construct(AnalyzerInterface $analyzer, RepositoryFactory $repositoryFactory, $rootDir)
    {
        $this->analyzer = $analyzer;
        $this->repositoryFactory = $repositoryFactory;
        $this->rootDir = $rootDir;
    }

    /**
     * Create build
     *
     * @param Project $project
     * @return Build
     */
    public function build(Project $project)
    {
        $projectDirectory = $this->getProjectDirectory($project);
        $workDirectory = $this->getWorkingDirectory($project);

        $repositoryConfig = $project->getRepositoryConfig();
        $repository = $this->repositoryFactory->factory($repositoryConfig, $projectDirectory);

        $sourceDirectory = $repository->getSourceDirectory();

        $versionStrategy = $repository->getVersionStrategy();
        $version = $versionStrategy->determineVersion($project);

        $tsStart = microtime(true);
        $classes = $this->analyzer->analyze($sourceDirectory, $workDirectory);
        $tsEnd = microtime(true);
        $runTime = $tsEnd - $tsStart;

        $build = new Build();
        $build
            ->setProject($project)
            ->setVersion($version)
            ->setGpa(1.23)
            ->setRunTime($runTime);

        return $build;
    }

    private function getProjectDirectory(Project $project)
    {
        return $this->rootDir . '/data/' . $project->getId();
    }

    private function getWorkingDirectory(Project $project)
    {
        return $this->getProjectDirectory($project) . '/work';
    }
}
