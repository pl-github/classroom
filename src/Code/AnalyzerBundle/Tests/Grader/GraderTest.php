<?php

namespace Code\AnalyzerBundle\Test\Grader;

use Code\AnalyzerBundle\Grader\Grader;
use Code\AnalyzerBundle\Result\Metric\Metric;
use Code\AnalyzerBundle\Result\Result;
use Code\AnalyzerBundle\Result\Smell\Smell;
use Code\AnalyzerBundle\Result\Source\SourceRange;
use Code\PhpAnalyzerBundle\Node\PhpClassNode;

class GraderTest extends \PHPUnit_Framework_TestCase
{
    public function testNodeWithoutSmells()
    {
        $classNode = new PhpClassNode('class1');
        $classNode->addMetric(new Metric('linesOfCode', 100));

        $grader = new Grader();
        $grade = $grader->grade($classNode, array());

        $this->assertEquals('A', $grade);
    }

    public function testNodeWithFewSmells()
    {
        $classNode = new PhpClassNode('class1');
        $classNode->addMetric(new Metric('linesOfCode', 100));

        $smells = array(
            new Smell('origin1', 'rule1', 'text1', new SourceRange(1, 2), 1),
            new Smell('origin2', 'rule2', 'text2', new SourceRange(3, 4), 2),
            new Smell('origin3', 'rule3', 'text3', new SourceRange(5, 6), 3),
        );

        $grader = new Grader();
        $grade = $grader->grade($classNode, $smells);

        $this->assertEquals('B', $grade);
    }

    public function testNodeWithManySmells()
    {
        $classNode = new PhpClassNode('class1');
        $classNode->addMetric(new Metric('linesOfCode', 100));

        $smells = array(
            new Smell('origin1', 'rule1', 'text1', new SourceRange(1, 2), 2),
            new Smell('origin2', 'rule2', 'text2', new SourceRange(3, 4), 5),
            new Smell('origin3', 'rule3', 'text3', new SourceRange(5, 6), 8),
        );

        $grader = new Grader();
        $grade = $grader->grade($classNode, $smells);

        $this->assertEquals('D', $grade);
    }
}
