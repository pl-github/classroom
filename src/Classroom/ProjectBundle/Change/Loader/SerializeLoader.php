<?php

namespace Classroom\ProjectBundle\Change\Loader;

use Classroom\ProjectBundle\Change\Changes;
use Classroom\ProjectBundle\Project;

class SerializeLoader implements LoaderInterface
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
     * @inheritDoc
     */
    public function load(Project $project)
    {
        $projectId = $project->getId();
        $filename = $this->dataDir . '/' . $projectId . '/changes.serialized';

        $data = file_get_contents($filename);
        $changes = unserialize($data);

        return $changes;
    }
}
