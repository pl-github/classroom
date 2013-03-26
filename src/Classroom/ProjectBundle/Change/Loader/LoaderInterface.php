<?php

namespace Classroom\ProjectBundle\Change\Loader;

use Classroom\ProjectBundle\Change\Changes;
use Classroom\ProjectBundle\Project;

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
