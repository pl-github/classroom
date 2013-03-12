<?php

namespace Code\ProjectBundle\Change\Loader;

use Code\ProjectBundle\Change\Changes;
use Code\ProjectBundle\Project;

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
