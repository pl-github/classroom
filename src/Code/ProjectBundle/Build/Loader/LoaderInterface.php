<?php

namespace Code\ProjectBundle\Build\Loader;

use Code\ProjectBundle\Build\Build;
use Code\ProjectBundle\Project;

interface LoaderInterface
{
    /**
     * Load build
     *
     * @param Project $project
     * @param mixed   $version
     * @return Build
     */
    public function load(Project $project, $version);
}
