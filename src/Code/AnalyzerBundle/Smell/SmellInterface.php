<?php

namespace Code\AnalyzerBundle\Smell;

use Code\AnalyzerBundle\Model\Referencable;

interface SmellInterface extends Referencable
{
    /**
     * Return id
     *
     * @return string
     */
    public function getId();

    /*+
     * Return origin
     *
     * @return string
     */
    public function getOrigin();

    /*+
     * Return rule
     *
     * @return string
     */
    public function getRule();

    /*+
     * Return text
     *
     * @return string
     */
    public function getText();

    /**
     * Return source range
     *
     * @return SourceRange
     */
    public function getSourceRange();

    /**
     * Set score
     *
     * @param integer $score
     * @return $this
     */
    public function setScore($score);

    /*+
     * Return score
     *
     * @return integer
     */
    public function getScore();
}
