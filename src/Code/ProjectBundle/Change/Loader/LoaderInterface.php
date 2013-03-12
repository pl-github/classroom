<?php

namespace Code\ProjectBundle\Change\Loader;

use Code\ProjectBundle\Change\Changes;
use Code\ProjectBundle\Project;

interface LoaderInterface
{
    /**
     * Load build
     *
     * @param Project $project
     * @return Changes
     */
    public function load(Project $project);
}
