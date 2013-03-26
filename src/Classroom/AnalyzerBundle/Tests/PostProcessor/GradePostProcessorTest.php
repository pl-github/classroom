<?php

namespace Classroom\AnalyzerBundle\Test\PostProcessor;

use Classroom\AnalyzerBundle\Log\Log;
use Classroom\AnalyzerBundle\PostProcessor\GradePostProcessor;
use Classroom\AnalyzerBundle\Result\Result;
use Classroom\AnalyzerBundle\Result\Smell\Smell;
use Classroom\AnalyzerBundle\Result\Source\SourceRange;
use Classroom\PhpAnalyzerBundle\Node\PhpClassNode;

class GradePostProcessorTest extends \PHPUnit_Framework_TestCase
{
    public function testCountGrades()
    {
        $result = $this->createResultWithNodesAndSmells();

        $graderMock = $this->getMockBuilder('Classroom\AnalyzerBundle\Grader\Grader')
            ->disableOriginalConstructor()
            ->getMock();

        $graderMock->expects($this->at(0))
            ->method('grade')
            ->will($this->returnValue('A'));

        $graderMock->expects($this->at(1))
            ->method('grade')
            ->will($this->returnValue('B'));

        $gradePostProcessor = new GradePostProcessor($graderMock);
        $gradePostProcessor->process($result);

        $this->assertEquals('A', $result->getNode('class1')->getGrade());
        $this->assertEquals('B', $result->getNode('class2')->getGrade());
    }

    private function createResultWithNodesAndSmells()
    {
        $result = new Result();
        $result->setLog(new Log());

        $classNode1 = new PhpClassNode('class1');
        $result->addNode($classNode1);

        $smell11 = new Smell('origin', 'rule', 'text', new SourceRange(1, 2), 1);
        $smell12 = new Smell('origin', 'rule', 'text', new SourceRange(1, 2), 2);
        $smell13 = new Smell('origin', 'rule', 'text', new SourceRange(1, 2), 3);

        $result->addSmell($smell11, $classNode1);
        $result->addSmell($smell12, $classNode1);
        $result->addSmell($smell13, $classNode1);

        $classNode2 = new PhpClassNode('class2');
        $result->addNode($classNode2);

        $smell21 = new Smell('origin', 'rule', 'text', new SourceRange(1, 2), 4);
        $smell22 = new Smell('origin', 'rule', 'text', new SourceRange(1, 2), 5);

        $result->addSmell($smell21, $classNode2);
        $result->addSmell($smell22, $classNode2);

        return $result;
    }
}
