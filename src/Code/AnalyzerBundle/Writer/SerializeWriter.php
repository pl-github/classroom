<?php

namespace Code\AnalyzerBundle\Writer;

use Code\AnalyzerBundle\Model\ResultModel;

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
    public function write(ResultModel $result)
    {
        $buildVersion = $build->getVersion();
        $project = $build->getProject();
        $projectId = $project->getId();

        $directory = $this->ensureDirectoryWritable($this->dataDir . '/' . $projectId . '/build');
        $filename = $directory . '/' . $buildVersion . '.serialized';
        $data = serialize($result);

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
