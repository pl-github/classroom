<?php

namespace Code\ProjectBundle\Build\VersionStrategy;

use Code\ProjectBundle\Project;

class IncrementalVersionStrategy implements VersionStrategyInterface
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
    public function nextVersion(Project $project)
    {
        $projectId = $project->getId();
        $versionFilename = $this->ensureDirectoryWritable($this->dataDir) . '/' . $projectId . '.version';

        $version = 0;
        if (file_exists($versionFilename)) {
            $version = (integer)file_get_contents($versionFilename);
        }

        $version++;

        file_put_contents($versionFilename, $version);

        return $version;
    }

    /**
     * Ensure directory exists and is writable
     *
     * @param string $directory
     * @return string
     * @throws \Exception
     */
    private function ensureDirectoryWritable($directory)
    {
        if (!file_exists($directory) && !mkdir($directory, 0777, true)) {
            throw new \Exception('Can\'t create data dir');
        }

        if (!is_writable($directory)) {
            throw new \Exception('Data dir not writable');
        }

        return $directory;
    }
}