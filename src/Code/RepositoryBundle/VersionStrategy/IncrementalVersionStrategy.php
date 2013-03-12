<?php

namespace Code\RepositoryBundle\VersionStrategy;

class IncrementalVersionStrategy implements VersionStrategyInterface
{
    /**
     * @var string
     */
    private $projectDirectory;

    /**
     * @param string $rootDir
     */
    public function __construct($projectDirectory)
    {
        $this->projectDirectory = $projectDirectory;
    }

    /**
     * @inheritDoc
     */
    public function determineVersion()
    {
        $versionFilename = $this->ensureDirectoryWritable($this->projectDirectory) . '/version';

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
