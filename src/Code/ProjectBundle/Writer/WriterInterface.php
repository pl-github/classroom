<?php

namespace Code\ProjectBundle\Writer;

use Code\ProjectBundle\Project;

interface WriterInterface
{
    /**
     * Write project
     *
     * @param Project $project
     */
    public function write(Project $project);
}
