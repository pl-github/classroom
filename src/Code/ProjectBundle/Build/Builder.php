<?php

namespace Code\ProjectBundle\Build;

use Code\AnalyzerBundle\Analyzer\AnalyzerInterface;
use Code\ProjectBundle\Build\VersionStrategy\VersionStrategyInterface;
use Code\ProjectBundle\Project;
use Code\RepositoryBundle\RepositoryFactory;
use Code\RepositoryBundle\RepositoryInterface;

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

        $sourceDirectory = $repository->getSourceDirectory() . '/' . $project->getLibDir();

        $versionStrategy = $repository->getVersionStrategy();
        $version = $versionStrategy->determineVersion($project);

        $classes = $this->analyzer->analyze($sourceDirectory, $workDirectory);

        $build = new Build($project, $version, $classes);

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
