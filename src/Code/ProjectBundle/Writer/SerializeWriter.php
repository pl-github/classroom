<?php

namespace Code\ProjectBundle\Writer;

use Code\ProjectBundle\Project;

class SerializeWriter implements WriterInterface
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
    public function write(Project $project)
    {
        $projectId = $project->getId();

        $directory = $this->ensureDirectoryWritable($this->dataDir . '/' . $projectId);
        $filename = $directory . '/project.serialized';
        $data = serialize($project);

        file_put_contents($filename, $data);
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
            throw new \Exception('Can\'t create data dir "' . $directory . '"');
        }

        if (!is_writable($directory)) {
            throw new \Exception('Data dir "' . $directory . '" not writable');
        }

        return $directory;
    }
}
