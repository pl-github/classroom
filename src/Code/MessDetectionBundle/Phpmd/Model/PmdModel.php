<?php

namespace Code\MessDetectionBundle\Phpmd\Model;

use Code\AnalyzerBundle\Analyzer\Model\ModelInterface;

class PmdModel implements ModelInterface
{
    /**
     * @var string
     */
    private $version;

    /**
     * @var \DateTime
     */
    private $timestamp;

    /**
     * @var array
     */
    private $files = array();

    /**
     * @param string    $version
     * @param \DateTime $timestamp
     * @param array     $files
     */
    public function __construct($version, \DateTime $timestamp, array $files = array())
    {
        $this->version = $version;
        $this->timestamp = $timestamp;
        $this->files = $files;
    }

    /**
     * Return version
     *
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Return timestamp
     *
     * @return \DateTime
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * Add file
     *
     * @param FileModel $file
     * @return $this
     */
    public function addFile(FileModel $file)
    {
        $this->files[] = $file;

        return $this;
    }

    /**
     * Return files
     *
     * @return array
     */
    public function getFiles()
    {
        return $this->files;
    }
}
