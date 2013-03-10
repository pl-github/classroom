<?php

namespace Code\MessDetectionBundle\Phpmd\Model;

class PmdModel
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var \DateTime
     */
    private $timestamp;

    /**
     * @var array
     */
    private $files = array();

    /**
     * @param string    $name
     * @param \DateTime $timestamp
     * @param array     $files
     */
    public function __construct($name, \DateTime $timestamp, array $files = array())
    {
        $this->name = $name;
        $this->timestamp = $timestamp;
        $this->files = $files;
    }

    /**
     * Return name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
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