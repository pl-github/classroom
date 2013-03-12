<?php

namespace Code\ProjectBundle\Change\Writer;

use Code\ProjectBundle\Change\Changes;
use Code\ProjectBundle\Project;

interface WriterInterface
{
    /**
     * Write changes
     *
     * @param Changes $changes
     * @param Project $project
     */
    public function write(Changes $changes, Project $project);
}
