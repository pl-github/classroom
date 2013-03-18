<?php

namespace Code\AnalyzerBundle\Model;

class SourceModel
{
    /**
     * @var array
     */
    private $source;

    /**
     * @var NodeReference
     */
    private $nodeReference;

    /**
     * @param string        $source
     * @param NodeReference $nodeReference
     */
    public function __construct($source, NodeReference $nodeReference)
    {
        $this->sources = $source;
        $this->nodeReference = $nodeReference;
    }

    /*+
     * Return source
     *
     * @return string
     */
    public function getSource()
    {
        return $this->sources;
    }

    /**
     * Return node reference
     *
     * @return NodeReference
     */
    public function getNodeReference()
    {
        return $this->nodeReference;
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
