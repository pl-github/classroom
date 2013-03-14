<?php

namespace Code\AnalyzerBundle\Model;

class SmellModel
{
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
     * @var SourceModel
     */
    private $source;

    /**
     * @var integer
     */
    private $score;

    /**
     * @param string      $origin
     * @param string      $rule
     * @param string      $text
     * @param SourceModel $source
     * @param integer     $score
     */
    public function __construct($origin, $rule, $text, $source = null, $score = null)
    {
        $this->origin = $origin;
        $this->rule = $rule;
        $this->text = $text;
        $this->source = $source;
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
     * Return source
     *
     * @return SourceModel
     */
    public function getSource()
    {
        return $this->source;
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
