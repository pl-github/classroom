<?php

namespace Code\AnalyzerBundle\Grader;

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
