<?php

namespace Code\AnalyzerBundle\Result\Source;

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

        if ($endLine === null) {
            $endLine = $beginLine;
        }

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
