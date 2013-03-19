<?php

namespace Code\AnalyzerBundle\Source\Storage;

class PharStorage implements StorageInterface
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
        return file_get_contents(\Phar::running() . $this->filename);
    }

    /**
     * @inheritDoc
     */
    public function getContentAsArray()
    {
        return file(\Phar::running() . $this->filename);
    }
}
