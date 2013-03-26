<?php

namespace Code\AnalyzerBundle\Test\Grader;

use Code\AnalyzerBundle\Grader\GradeCounter;
use Code\AnalyzerBundle\Result\Result;
use Code\PhpAnalyzerBundle\Node\PhpClassNode;
use Code\PhpAnalyzerBundle\Node\PhpFileNode;

class GradeCounterTest extends \PHPUnit_Framework_TestCase
{
    public function testCountGrades()
    {
        $result = $this->createResultWithGradedNodes();

        $counter = new GradeCounter();
        $breakdown = $counter->countGrades($result);

        $this->assertEquals(array('A' => 1, 'B' => 2, 'C' => 3, 'D' => 2, 'F' => 1), $breakdown);
    }

    private function createResultWithGradedNodes()
    {
        $result = new Result();

        $classNode1 = new PhpClassNode('class1');
        $classNode1->setGrade('A');
        $result->addNode($classNode1);

        $classNode2 = new PhpClassNode('class2');
        $classNode2->setGrade('B');
        $result->addNode($classNode2);

        $classNode3 = new PhpClassNode('class3');
        $classNode3->setGrade('B');
        $result->addNode($classNode3);

        $classNode4 = new PhpClassNode('class4');
        $classNode4->setGrade('C');
        $result->addNode($classNode4);

        $classNode5 = new PhpClassNode('class5');
        $classNode5->setGrade('C');
        $result->addNode($classNode5);

        $classNode6 = new PhpClassNode('class6');
        $classNode6->setGrade('C');
        $result->addNode($classNode6);

        $classNode7 = new PhpClassNode('class7');
        $classNode7->setGrade('D');
        $result->addNode($classNode7);

        $classNode8 = new PhpClassNode('class8');
        $classNode8->setGrade('D');
        $result->addNode($classNode8);

        $classNode9 = new PhpClassNode('class9');
        $classNode9->setGrade('F');
        $result->addNode($classNode9);

        return $result;
    }
}
