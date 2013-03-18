<?php

namespace Code\AnalyzerBundle\Loader;

use Code\AnalyzerBundle\Model\ClassesModel;
use Code\BuildBundle\Build;
use Code\ProjectBundle\Project;

interface LoaderInterface
{
    /**
     * Load classes
     *
     * @param Project $project
     * @param mixed   $version
     * @return ClassesModel
     */
    public function load(Project $project, $version);
}
