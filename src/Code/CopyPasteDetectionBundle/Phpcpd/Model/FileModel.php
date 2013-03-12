<?php

namespace Code\CopyPasteDetectionBundle\Phpcpd\Model;

class FileModel
{
    /**
     * @var string
     */
    private $path = '';

    /**
     * @var integer
     */
    private $line = 0;

    /**
     * @param string  $path
     * @param integer $line
     */
    public function __construct($path, $line)
    {
        $this->path = $path;
        $this->line = $line;
    }

    /**
     * Return path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Return line number
     *
     * @return integer
     */
    public function getLine()
    {
        return $this->line;
    }
}
