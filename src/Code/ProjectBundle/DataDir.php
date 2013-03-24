<?php

namespace Code\ProjectBundle;

use Code\ProjectBundle\Entity\Project;

class DataDir
{
    /**
     * @var string
     */
    private $baseDir;

    /**
     * @param string $baseDir
     */
    public function __construct($baseDir)
    {
        $this->baseDir = $baseDir;
    }

    /**
     * Return base dir
     *
     * @return string
     */
    public function getBaseDirectory()
    {
        return $this->baseDir;
    }

    /**
     * Return subdir
     *
     * @param string $suffix
     * @return string
     */
    public function getDirectory($suffix)
    {
        return $this->ensureDirectoryWritable($this->getBaseDirectory() . '/' . $suffix);
    }

    /**
     * Return working directory
     *
     * @return string
     */
    public function getWorkingDirectory()
    {
        return $this->getDirectory('/work');
    }

    /**
     * Return builds directory
     *
     * @return string
     */
    public function getBuildsDirectory()
    {
        return $this->getDirectory('/builds');
    }

    /**
     * Return temp directory
     *
     * @return string
     */
    public function getTempDirectory()
    {
        return $this->getDirectory('/temp');
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
            throw new \Exception('Can\'t create requested directory.');
        }

        if (!is_writable($directory)) {
            throw new \Exception('Requested directory is not writable.');
        }

        return $directory;
    }
}