<?php

namespace Code\AnalyzerBundle\Model;

class SourceRange
{
    /**
     * @var integer
     */
    private $beginLine;

    /**
     * @var integer
     */
    private $endLine;

    /**
     * @param integer $beginLine
     * @param integer $endLine
     */
    public function __construct($beginLine, $endLine = null)
    {
        $this->beginLine = $beginLine;
        $this->endLine = $endLine;
    }

    /*+
     * Return begin line
     *
     * @return integer
     */
    public function getBeginLine()
    {
        return $this->beginLine;
    }

    /*+
     * Return end line
     *
     * @return integer
     */
    public function getEndLine()
    {
        return $this->endLine;
    }
}
