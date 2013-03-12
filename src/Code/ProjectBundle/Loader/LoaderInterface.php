<?php

namespace Code\ProjectBundle\Loader;

use Code\ProjectBundle\Project;

interface LoaderInterface
{
    /**
     * Load project
     *
     * @param mixed $projectId
     * @return Project
     */
    public function load($projectId);
}
