<?php

namespace Code\AnalyzerBundle\Tests\Merger;

use Code\AnalyzerBundle\Merger\ClassesMerger;
use Code\AnalyzerBundle\Model\ClassesModel;
use Code\AnalyzerBundle\Model\ClassModel;
use Code\AnalyzerBundle\Model\MetricModel;
use Code\AnalyzerBundle\Model\SmellModel;

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
        $class1 = new ClassModel('class1', 'namespace1');
        $metric1 = new MetricModel('key1', 'value1');
        $class1->addMetric($metric1);
        $smell1 = new SmellModel('origin1', 'text1');
        $class1->addSmell($smell1);
        $classes1->addClass($class1);

        $classes2 = new ClassesModel();
        $class2 = new ClassModel('class2', 'namespace2');
        $metric2 = new MetricModel('key2', 'value2');
        $class2->addMetric($metric2);
        $smell2 = new SmellModel('origin2', 'text2');
        $class2->addSmell($smell2);
        $classes2->addClass($class2);

        $classes3 = new ClassesModel();
        $class3 = new ClassModel('class1', 'namespace1');
        $metric3 = new MetricModel('key3', 'value3');
        $class3->addMetric($metric3);
        $smell3 = new SmellModel('origin3', 'text3');
        $class3->addSmell($smell3);
        $classes3->addClass($class3);

        $classes4 = new ClassesModel();
        $class4 = new ClassModel('class4', 'namespace1');
        $metric4 = new MetricModel('key4', 'value4');
        $class4->addMetric($metric4);
        $smell4 = new SmellModel('origin4', 'text4');
        $class4->addSmell($smell4);
        $classes4->addClass($class4);

        $classes = $this->merger->merge($classes1, $classes2, $classes3, $classes4);

        $this->assertEquals(3, count($classes->getClasses()));

        return $classes;
    }

    /**
     * @depends testMergedClasses
     */
    public function testMergedClass1(ClassesModel $classes)
    {
        $class = $classes->getClass('namespace1\class1');

        $this->assertEquals('class1', $class->getName());

        return $class;
    }

    /**
     * @depends testMergedClass1
     */
    public function testMergedMetrics1(ClassModel $class)
    {
        $metrics = $class->getMetrics();

        $this->assertEquals(2, count($metrics));

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

        $this->assertEquals(2, count($smells));

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
        $class = $classes->getClass('namespace2\class2');

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

    public function testMergeWithArray()
    {
        $classes1 = new ClassesModel(array(new ClassModel('class1')));
        $classes2 = new ClassesModel(array(new ClassModel('class2')));
        $classes3 = new ClassesModel(array(new ClassModel('class3')));

        $classes = $this->merger->merge(array($classes1, $classes2, $classes3));

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
     * @expectedException \InvalidArgumentException
     */
    public function testMergeThrowsInvalidArgumentExceptionOnWrongClass()
    {
        $classes = new ClassesModel(array(new ClassModel('class1')));
        $class = new ClassModel('class2');

        $this->merger->merge($classes, $class);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testMergeThrowsInvalidArgumentExceptionOnWrongArguments()
    {
        $classes1 = new ClassesModel(array(new ClassModel('class1')));

        $this->merger->merge($classes1);
    }
}
