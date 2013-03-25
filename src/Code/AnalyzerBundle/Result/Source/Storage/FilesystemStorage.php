<?php

namespace Code\AnalyzerBundle\Result\Source\Storage;

class FilesystemStorage implements StorageInterface
{
    /**
     * @var string
     */
    private $filename;

    /**
     * @param string $filename
     */
    public function __construct($filename)
    {
        $this->filename = $filename;
    }

    /**
     * Set filename
     *
     * @param string $filename
     * @return $this
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * Return filename
     *
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * @inheritDoc
     */
    public function getContent()
    {
        return file_get_contents($this->filename);
    }

    /**
     * @inheritDoc
     */
    public function getContentAsArray()
    {
        return file($this->filename, FILE_IGNORE_NEW_LINES);
    }
}
