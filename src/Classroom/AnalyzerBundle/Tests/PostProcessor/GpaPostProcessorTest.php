<?php

namespace Classroom\AnalyzerBundle\Test\PostProcessor;

use Classroom\AnalyzerBundle\Gpa\GpaCalculator;
use Classroom\AnalyzerBundle\Grader\GradeCounter;
use Classroom\AnalyzerBundle\Log\Log;
use Classroom\AnalyzerBundle\PostProcessor\GpaPostProcessor;
use Classroom\AnalyzerBundle\Result\Result;
use Classroom\AnalyzerBundle\Result\Smell\Smell;
use Classroom\AnalyzerBundle\Result\Source\SourceRange;
use Classroom\PhpAnalyzerBundle\Node\PhpClassNode;

class GpaPostProcessorTest extends \PHPUnit_Framework_TestCase
{
    public function testCountGrades()
    {
        $result = new Result();
        $result->setLog(new Log());

        $gradeCounterMock = $this->getMockBuilder('Classroom\AnalyzerBundle\Grader\GradeCounter')
            ->disableOriginalConstructor()
            ->getMock();

        $gradeCounterMock->expects($this->any())
            ->method('countGrades')
            ->will($this->returnValue(array('A' => 1, 'B' => 0, 'C' => 1, 'D' => 0, 'F' => 1)));

        $gpaCalculatorMock = $this->getMockBuilder('Classroom\AnalyzerBundle\Gpa\GpaCalculator')
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
