<?php

namespace Classroom\AnalyzerBundle\Result\Smell;

use Classroom\AnalyzerBundle\Result\Reference\Referencable;
use Classroom\AnalyzerBundle\Result\Source\SourceRange;

interface SmellInterface extends Referencable
{
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
