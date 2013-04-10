<?php

namespace Classroom\AnalyzerBundle\Grader;

trait GradableTrait
{
    /**
     * @var string
     */
    private $grade;

    /**
     * Return grade
     *
     * @return string
     */
    public function getGrade()
    {
        return $this->grade;
    }

    /**
     * Set grade
     *
     * @param string $grade
     * @return string
     */
    public function setGrade($grade)
    {
        $this->grade = $grade;
    }

}
