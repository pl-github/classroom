<?php

namespace Code\ProjectBundle\Loader;

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
    public function load($projectId)
    {
        $filename = $this->dataDir . '/' . $projectId . '/project.serialized';

        $data = file_get_contents($filename);

        $project = unserialize($data);

        return $project;
    }
}
