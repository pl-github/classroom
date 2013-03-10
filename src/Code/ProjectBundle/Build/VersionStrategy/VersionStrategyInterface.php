<?php

namespace Code\ProjectBundle\Build\VersionStrategy;

use Code\ProjectBundle\Project;

interface VersionStrategyInterface
{
    /**
     * @param Project $project
     * @return mixed
     */
    public function nextVersion(Project $project);
}