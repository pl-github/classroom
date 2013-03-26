<?php

namespace Classroom\ProjectBundle\Change\Writer;

use Classroom\ProjectBundle\Change\Changes;
use Classroom\ProjectBundle\Project;

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
