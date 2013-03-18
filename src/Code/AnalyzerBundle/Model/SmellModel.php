<?php

namespace Code\AnalyzerBundle\Model;

class SmellModel
{
    /**
     * @var NodeReference
     */
    private $nodeReference;

    /**
     * @var string
     */
    private $origin;

    /**
     * @var string
     */
    private $rule;

    /**
     * @var string
     */
    private $text;

    /**
     * @var SourceRange
     */
    private $sourceRange;

    /**
     * @var integer
     */
    private $score;

    /**
     * @param NodeReference $nodeReference
     * @param string        $origin
     * @param string        $rule
     * @param string        $text
     * @param SourceRange   $sourceRange
     * @param integer       $score
     */
    public function __construct(NodeReference $nodeReference, $origin, $rule, $text, SourceRange $sourceRange, $score)
    {
        $this->nodeReference = $nodeReference;
        $this->origin = $origin;
        $this->rule = $rule;
        $this->text = $text;
        $this->sourceRange = $sourceRange;
        $this->score = $score;
    }

    /**
     * Return id
     *
     * @return string
     */
    public function getId()
    {
        return spl_object_hash($this);
    }

    /*+
     * Return node reference
     *
     * @return NodeReference
     */
    public function getNodeReference()
    {
        return $this->nodeReference;
    }

    /*+
     * Return origin
     *
     * @return string
     */
    public function getOrigin()
    {
        return $this->origin;
    }

    /*+
     * Return rule
     *
     * @return string
     */
    public function getRule()
    {
        return $this->rule;
    }

    /*+
     * Return text
     *
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Return source range
     *
     * @return SourceRange
     */
    public function getSourceRange()
    {
        return $this->sourceRange;
    }

    /**
     * Set score
     *
     * @param integer $score
     * @return $this
     */
    public function setScore($score)
    {
        $this->score = $score;

        return $this;
    }

    /*+
     * Return score
     *
     * @return integer
     */
    public function getScore()
    {
        return $this->score;
    }
}
