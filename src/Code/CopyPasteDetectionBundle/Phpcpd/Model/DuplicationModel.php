<?php

namespace Code\CopyPasteDetectionBundle\Phpcpd\Model;

class DuplicationModel
{
    /**
     * @var integer
     */
    private $lines;

    /**
     * @var integer
     */
    private $tokens;

    /**
     * @var string
     */
    private $codefragment;

    /**
     * @var array
     */
    private $files = array();

    /**
     * @param integer $lines
     * @param integer $tokens
     * @param string  $codefragment
     * @param array   $files
     */
    public function __construct($lines, $tokens, $codefragment, array $files = array())
    {
        $this->lines = $lines;
        $this->tokens = $tokens;
        $this->codefragment = $codefragment;
        $this->files = $files;
    }

    /**
     * Return number of lines
     *
     * @return integer
     */
    public function getLines()
    {
        return $this->lines;
    }

    /**
     * Return number of tokens
     *
     * @return integer
     */
    public function getTokens()
    {
        return $this->tokens;
    }

    /**
     * Return codefragment
     *
     * @return string
     */
    public function getCodefragment()
    {
        return $this->codefragment;
    }

    /**
     * Add file
     *
     * @param FileModel $file
     * @return $this;
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
