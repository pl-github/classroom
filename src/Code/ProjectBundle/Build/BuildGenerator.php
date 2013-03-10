<?php

namespace Code\ProjectBundle\Build;

use Code\ProjectBundle\Build\VersionStrategy\VersionStrategyInterface;
use Code\ProjectBundle\Project;

class BuildGenerator
{
    /**
     * @var VersionStrategyInterface
     */
    protected $versionStrategy;

    /**
     * @param VersionStrategyInterface $versionStrategy
     */
    public function __construct(VersionStrategyInterface $versionStrategy)
    {
        $this->versionStrategy = $versionStrategy;
    }

    /**
     * Create build
     *
     * @param Project $project
     * @return Build
     */
    public function createBuild(Project $project)
    {
        $build = new Build($project);
        $version = $this->versionStrategy->nextVersion($project);
        $build->setVersion($version);

        return $build;
    }
}