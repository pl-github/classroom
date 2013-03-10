<?php

namespace Code\ProjectBundle\Tests\Merger;

use Code\ProjectBundle\Merger\ClassesMerger;
use Code\ProjectBundle\Model\ClassesModel;
use Code\ProjectBundle\Model\ClassModel;
use Code\ProjectBundle\Model\MetricModel;
use Code\ProjectBundle\Model\SmellModel;

class ClassesMergerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ClassesMerger
     */
    private $merger;

    public function setUp()
    {
        $this->merger = new ClassesMerger();
    }

    public function tearDown()
    {
        $this->merger = null;
    }

    public function testMergedClasses()
    {
        $classes1 = new ClassesModel();
        $class1 = new ClassModel('class1');
        $metric1 = new MetricModel('key1', 'value1');
        $class1->addMetric($metric1);
        $smell1 = new SmellModel('origin1', 'text1');
        $class1->addSmell($smell1);
        $classes1->addClass($class1);

        $classes2 = new ClassesModel();
        $class2 = new ClassModel('class2');
        $metric2 = new MetricModel('key2', 'value2');
        $class2->addMetric($metric2);
        $smell2 = new SmellModel('origin2', 'text2');
        $class2->addSmell($smell2);
        $classes2->addClass($class2);

        $classes = $this->merger->merge($classes1, $classes2);

        $this->assertEquals(2, count($classes->getClasses()));

        return $classes;
    }

    /**
     * @depends testMergedClasses
     */
    public function testMergedClass1(ClassesModel $classes)
    {
        $class = $classes->getClass('class1');

        $this->assertEquals('class1', $class->getName());

        return $class;
    }

    /**
     * @depends testMergedClass1
     */
    public function testMergedMetrics1(ClassModel $class)
    {
        $metrics = $class->getMetrics();

        $this->assertEquals(1, count($metrics));

        $metric = current($metrics);

        $this->assertEquals('key1', $metric->getKey());
        $this->assertEquals('value1', $metric->getValue());

        return $class;
    }

    /**
     * @depends testMergedClass1
     */
    public function testMergedSmells1(ClassModel $class)
    {
        $smells = $class->getSmells();

        $this->assertEquals(1, count($smells));

        $smell = current($smells);

        $this->assertEquals('origin1', $smell->getOrigin());
        $this->assertEquals('text1', $smell->getText());

        return $class;
    }

    /**
     * @depends testMergedClasses
     */
    public function testMergedClass2(ClassesModel $classes)
    {
        $class = $classes->getClass('class2');

        $this->assertEquals('class2', $class->getName());

        return $class;
    }

    /**
     * @depends testMergedClass2
     */
    public function testMergedMetrics2(ClassModel $class)
    {
        $metrics = $class->getMetrics();

        $this->assertEquals(1, count($metrics));

        $metric = current($metrics);

        $this->assertEquals('key2', $metric->getKey());
        $this->assertEquals('value2', $metric->getValue());

        return $class;
    }
    /**
     * @depends testMergedClass2
     */
    public function testMergedSmells2(ClassModel $class)
    {
        $smells = $class->getSmells();

        $this->assertEquals(1, count($smells));

        $smell = current($smells);

        $this->assertEquals('origin2', $smell->getOrigin());
        $this->assertEquals('text2', $smell->getText());

        return $class;
    }

    public function testMergeThreeClasses()
    {
        $classes1 = new ClassesModel(array(new ClassModel('class1')));
        $classes2 = new ClassesModel(array(new ClassModel('class2')));
        $classes3 = new ClassesModel(array(new ClassModel('class3')));

        $classes = $this->merger->merge($classes1, $classes2, $classes3);

        $this->assertEquals(3, count($classes->getClasses()));
    }

    public function testMergeReusesAlreadyMergedClass()
    {
        $classes1 = new ClassesModel(array(new ClassModel('class1')));
        $classes2 = new ClassesModel(array(new ClassModel('class1')));

        $classes = $this->merger->merge($classes1, $classes2);

        $this->assertEquals(1, count($classes->getClasses()));
    }

    /**
     * @expectedException \Exception
     */
    public function testMergeThrowsExceptionOnWrongClass()
    {
        $classes = new ClassesModel(array(new ClassModel('class1')));
        $class = new ClassModel('class2');

        $classes = $this->merger->merge($classes, $class);
    }
}
