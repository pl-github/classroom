<?php

namespace Code\AnalyzerBundle\PostProcessor;

use Code\AnalyzerBundle\Gpa\GpaCalculator;
use Code\AnalyzerBundle\Grader\GradeCounter;
use Code\AnalyzerBundle\Result\Result;

class GpaPostProcessor implements PostProcessorInterface
{
    /**
     * @var GradeCounter
     */
    private $gradeCounter;

    /**
     * @var GpaCalculator
     */
    private $gpaCalculator;

    /**
     * @param GradeCounter  $gradeCounter
     * @param GpaCalculator $gpaCalculator
     */
    public function __construct(GradeCounter $gradeCounter, GpaCalculator $gpaCalculator)
    {
        $this->gradeCounter = $gradeCounter;
        $this->gpaCalculator = $gpaCalculator;
    }

    /**
     * @inheritDoc
     */
    public function process(Result $result)
    {
        $result->getLog()->addProcess('(GpaPostProcessor) Count grades');

        $breakdown = $this->gradeCounter->countGrades($result);
        $result->setBreakdown($breakdown);

        $result->getLog()->addProcess('(GpaPostProcessor) Calculate GPA');

        $gpa = $this->gpaCalculator->calculateGpaFromBreakdown($breakdown);
        $result->setGpa($gpa);
    }
}
