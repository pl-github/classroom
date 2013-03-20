<?php

namespace Code\AnalyzerBundle\Smell;

use Code\AnalyzerBundle\Source\SourceRange;

class Smell implements SmellInterface
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
     * @var SourceRange
     */
    private $sourceRange;

    /**
     * @var integer
     */
    private $score;

    /**
     * @var string
     */
    private $hash;

    /**
     * @param string        $origin
     * @param string        $rule
     * @param string        $text
     * @param SourceRange   $sourceRange
     * @param integer       $score
     */
    public function __construct($origin, $rule, $text, SourceRange $sourceRange, $score)
    {
        $this->origin = $origin;
        $this->rule = $rule;
        $this->text = $text;
        $this->sourceRange = $sourceRange;
        $this->score = $score;

        $this->hash = sha1(uniqid('bla', true) . rand(0, 99999));
    }

    /**
     * @inheritDoc
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * @inheritDoc
     */
    public function setHash($hash)
    {
        $this->hash = $hash;

        return $this;
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
