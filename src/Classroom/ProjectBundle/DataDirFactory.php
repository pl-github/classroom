<?php

namespace Classroom\ProjectBundle;

use Classroom\ProjectBundle\Entity\Project;

class DataDirFactory
{
    /**
     * @var string
     */
    private $dataDir;

    /**
     * @param string $rootDir
     */
    public function __construct($rootDir)
    {
        $this->dataDir = $rootDir . '/data';
    }

    /**
     * @param Project $project
     */
    public function factory(Project $project)
    {
        $projectDir = $this->dataDir . '/' . $project->getKey();

        return new DataDir($projectDir);
    }
}
