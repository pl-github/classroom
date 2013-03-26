<?php

namespace Code\AnalyzerBundle\Test\PostProcessor;

use Code\AnalyzerBundle\Gpa\GpaCalculator;
use Code\AnalyzerBundle\Grader\GradeCounter;
use Code\AnalyzerBundle\Log\Log;
use Code\AnalyzerBundle\PostProcessor\GpaPostProcessor;
use Code\AnalyzerBundle\Result\Result;
use Code\AnalyzerBundle\Result\Smell\Smell;
use Code\AnalyzerBundle\Result\Source\SourceRange;
use Code\PhpAnalyzerBundle\Node\PhpClassNode;

class GpaPostProcessorTest extends \PHPUnit_Framework_TestCase
{
    public function testCountGrades()
    {
        $result = new Result();
        $result->setLog(new Log());

        $gradeCounterMock = $this->getMockBuilder('Code\AnalyzerBundle\Grader\GradeCounter')
            ->disableOriginalConstructor()
            ->getMock();

        $gradeCounterMock->expects($this->any())
            ->method('countGrades')
            ->will($this->returnValue(array('A' => 1, 'B' => 0, 'C' => 1, 'D' => 0, 'F' => 1)));

        $gpaCalculatorMock = $this->getMockBuilder('Code\AnalyzerBundle\Gpa\GpaCalculator')
            ->disableOriginalConstructor()
            ->getMock();

        $gpaCalculatorMock->expects($this->once())
            ->method('calculateGpaFromBreakdown')
            ->will($this->returnValue(2.5));

        $gradePostProcessor = new GpaPostProcessor($gradeCounterMock, $gpaCalculatorMock);
        $gradePostProcessor->process($result);

        $this->assertEquals(2.5, $result->getGpa());
        $this->assertEquals(array('A' => 1, 'B' => 0, 'C' => 1, 'D' => 0, 'F' => 1), $result->getBreakdown());
    }
}
