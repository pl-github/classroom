<?php

namespace Code\AnalyzerBundle\Model;

class SourceModel
{
    /**
     * @var array
     */
    private $sourcesLines;

    /**
     * @var integer
     */
    private $from;

    /**
     * @var integer
     */
    private $to;

    /**
     * @var integer
     */
    private $surround;

    /**
     * @var array
     */
    private $files = array();

    /**
     * @param array   $files
     * @param integer $from
     * @param integer $to
     * @param integer $surround
     * @param array   $files
     */
    public function __construct(array $sourceLines, $from = null, $to = null, $surround = null, array $files = array())
    {
        $this->sourcesLines = $sourceLines;
        $this->from = $from;
        $this->to = $to;
        $this->surround= $surround;
        $this->files = $files;
    }


    /*+
     * Return sourceLines
     *
     * @return array
     */
    public function getSourceLines()
    {
        return $this->sourcesLines;
    }

    /*+
     * Return from
     *
     * @return integer
     */
    public function getFrom()
    {
        return $this->from;
    }

    /*+
     * Return to
     *
     * @return integer
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * Return surround
     *
     * @return integer
     */
    public function getSurround()
    {
        return $this->surround;
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

    public function getSourceString()
    {
        $from = max(($this->from - 1) - $this->surround, 0);
        $to = (($this->to) - $from) + $this->surround;

        $sourceLines = array_slice($this->sourcesLines, $from, $to, true);

        $padLength = strlen((string)$to);

        foreach ($sourceLines as $line => $sourceLine) {
            if ($line >= $this->from - 1 && $line <= $this->to - 1) {
                $sourceLine = '<span style="color: red;">' . $sourceLine . '</span>';
            }
            $sourceLines[$line] = str_pad($line + 1, $padLength, '0', STR_PAD_LEFT) . ': ' . $sourceLine;
        }

        return implode(PHP_EOL, $sourceLines);
    }
}
