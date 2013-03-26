<?php

namespace Classroom\AnalyzerBundle\Test\Gpa;

use Classroom\AnalyzerBundle\Gpa\GpaCalculator;
use Classroom\AnalyzerBundle\Grader\GradeCounter;
use Classroom\AnalyzerBundle\Log\Log;
use Classroom\AnalyzerBundle\PostProcessor\GpaPostProcessor;
use Classroom\AnalyzerBundle\Result\Result;
use Classroom\AnalyzerBundle\Result\Smell\Smell;
use Classroom\AnalyzerBundle\Result\Source\SourceRange;
use Classroom\PhpAnalyzerBundle\Node\PhpClassNode;

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
