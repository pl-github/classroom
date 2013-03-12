<?php

namespace Code\ProjectBundle\Build\Loader;

use Code\ProjectBundle\Build\Build;
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
    public function load(Project $project, $version)
    {
        $projectId = $project->getId();

        $filename = $this->dataDir . '/' . $projectId . '/build/' . $version . '.serialized';

        $data = file_get_contents($filename);

        $build = unserialize($data);

        return $build;
    }
}
