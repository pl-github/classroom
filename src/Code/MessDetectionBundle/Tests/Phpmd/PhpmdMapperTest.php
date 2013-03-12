<?php

namespace Code\MessDetectionBundle\Tests\Phpmd\PhpmdParser;

use Code\AnalyzerBundle\Model\ClassesModel;
use Code\AnalyzerBundle\Model\ClassModel;
use Code\MessDetectionBundle\Phpmd\Model\PmdModel;
use Code\MessDetectionBundle\Phpmd\Model\FileModel;
use Code\MessDetectionBundle\Phpmd\Model\ViolationModel;
use Code\MessDetectionBundle\Phpmd\PhpmdMapper;
use org\bovigo\vfs\vfsStream;

class PhpcpdMapperTest extends \PHPUnit_Framework_TestCase
{
    public function testMap()
    {
        $reflectionServiceMock = $this->getMockBuilder('Code\AnalyzerBundle\ReflectionService')
            ->disableOriginalConstructor()
            ->getMock();

        $reflectionServiceMock
            ->expects($this->any())
            ->method('getClassnameForFile')
            ->will($this->returnArgument(0));

        $pmd = new PmdModel(
            '123',
            new \DateTime('2012-12-12 12:12:12'),
            array(
                new FileModel(
                    'file1',
                    array(
                        new ViolationModel(0, 5, 'rule1', 'ruleset1', 'url1', 1, 'text1'),
                        new ViolationModel(5, 15, 'rule2', 'ruleset2', 'url2', 2, 'text2')
                    )
                ),
                new FileModel(
                    'file2',
                    array(
                        new ViolationModel(20, 30, 'rule3', 'ruleset3', 'url3', 3, 'text3'),
                        new ViolationModel(40, 100, 'rule4', 'ruleset4', 'url4', 4, 'text4'),
                        new ViolationModel(150, 300, 'rule5', 'ruleset5', 'url5', 5, 'text5')
                    )
                )
            )
        );

        $mapper = new PhpmdMapper($reflectionServiceMock);
        $classes = $mapper->map($pmd);

        $this->assertEquals(2, count($classes->getClasses()));

        return $classes;
    }

    /**
     * @depends testMap
     * @param ClassesModel $classes
     */
    public function testClass1(ClassesModel $classes)
    {
        $classes = $classes->getClasses();
        $class = $classes['file1'];
        /* @var $class ClassModel */

        $this->assertEquals('file1', $class->getName());
        $this->assertEquals(2, count($class->getSmells()));
    }

    /**
     * @depends testMap
     * @param ClassesModel $classes
     */
    public function testClass2(ClassesModel $classes)
    {
        $classes = $classes->getClasses();
        $class = $classes['file2'];
        /* @var $class ClassModel */

        $this->assertEquals('file2', $class->getName());
        $this->assertEquals(3, count($class->getSmells()));
    }
}
