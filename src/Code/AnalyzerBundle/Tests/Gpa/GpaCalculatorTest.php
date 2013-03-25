<?php

namespace Code\AnalyzerBundle\Test\Gpa;

use Code\AnalyzerBundle\Gpa\GpaCalculator;
use Code\AnalyzerBundle\Grader\GradeCounter;
use Code\AnalyzerBundle\Log\Log;
use Code\AnalyzerBundle\PostProcessor\GpaPostProcessor;
use Code\AnalyzerBundle\Result\Result;
use Code\AnalyzerBundle\Result\Smell\Smell;
use Code\AnalyzerBundle\Result\Source\SourceRange;
use Code\PhpAnalyzerBundle\Node\PhpClassNode;

class GpaCalculatorTest extends \PHPUnit_Framework_TestCase
{
    public function testCountGrades()
    {
        $breakdown = array('A' => 5, 'B' => 1, 'C' => 1, 'D' => 2, 'F' => 1);

        $calculator = new GpaCalculator();
        $gpa = $calculator->calculateGpaFromBreakdown($breakdown);

        $this->assertEquals(2.7, $gpa);
    }
}
