<?php

namespace Classroom\ProjectBundle;

use Classroom\ProjectBundle\Entity\Project;

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
     * Return subdir
     *
     * @param string $suffix
     * @return string
     */
    public function getDirectory($suffix = null)
    {
        return $this->ensureDirectoryWritable($this->getBaseDirectory() . $suffix);
    }

    /**
     * Return file
     *
     * @param string $filename
     * @return string
     */
    public function getFile($filename)
    {
        $directory = $this->getDirectory();

        return $directory . '/' . $filename;
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
     * Return wroking file
     *
     * @param string $filename
     * @return string
     */
    public function getWorkingFile($filename)
    {
        $directory = $this->getWorkingDirectory();

        return $directory . '/' . $filename;
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
     * Return build file
     *
     * @param string $filename
     * @return string
     */
    public function getBuildFile($filename)
    {
        $directory = $this->getBuildsDirectory();

        return $directory . '/' . $filename;
    }

    /**
     * Return temp directory
     *
     * @return string
     */
    public function getTempDirectory($filename = null)
    {
        return $this->getDirectory('/temp');
    }

    /**
     * Return temp file
     *
     * @param string $filename
     * @return string
     */
    public function getTempFile($filename)
    {
        $directory = $this->getTempDirectory();

        return $directory . '/' . $filename;
    }

    /**
     * Return base dir
     *
     * @return string
     */
    private function getBaseDirectory()
    {
        return $this->baseDir;
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
