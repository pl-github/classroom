<?php

namespace Code\AnalyzerBundle\Node;

use Code\AnalyzerBundle\Model\Referencable;

interface Gradable
{
    /**
     * Return grade
     *
     * @return string
     */
    public function getGrade();

    /**
     * Set grade
     *
     * @param string $grade
     * @return string
     */
    public function setGrade($grade);
}
