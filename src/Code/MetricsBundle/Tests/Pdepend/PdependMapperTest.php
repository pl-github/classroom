<?php

namespace Code\MetricsBundle\Tests\Pdepend\PdependParser;

use Code\AnalyzerBundle\Model\ClassesModel;
use Code\AnalyzerBundle\Model\ClassModel;
use Code\MetricsBundle\Pdepend\Model\MetricsModel as PdependMetricsModel;
use Code\MetricsBundle\Pdepend\Model\PackageModel as PdependPackageModel;
use Code\MetricsBundle\Pdepend\Model\ClassModel as PdependClassModel;
use Code\MetricsBundle\Pdepend\Model\MethodModel as PdependMethodModel;
use Code\MetricsBundle\Pdepend\PdependMapper;
use org\bovigo\vfs\vfsStream;

class PdependMapperTest extends \PHPUnit_Framework_TestCase
{
    public function testMap()
    {
        $reflectionService = $this->getMockBuilder('Code\AnalyzerBundle\ReflectionService')
            ->disableOriginalConstructor()
            ->getMock();

        $reflectionService
            ->expects($this->any())
            ->method('getClassnameForFile')
            ->will($this->returnArgument(0));

        $pdepend = new PdependMetricsModel(
            new \DateTime('2012-12-12 12:12:12'),
            '123',
            array(),
            array(
                new PdependPackageModel(
                    'package1',
                    array(),
                    array(
                        new PdependClassModel(
                            'class1',
                            'file1.php',
                            array('loc' => 5, 'eloc' => 10, 'nom' => 15, 'wmc' => 20, 'wmcnp' => 25),
                            array(
                                new PdependMethodModel(
                                    'method1',
                                    array('ccn' => 30)
                                )
                            )
                        ),
                    )
                ),
                new PdependPackageModel(
                    'package2',
                    array(),
                    array(
                        new PdependClassModel(
                            'class2',
                            'file2.php',
                            array('loc' => 5, 'eloc' => 10, 'nom' => 15, 'wmc' => 20, 'wmcnp' => 25),
                            array(
                                new PdependMethodModel(
                                    'method2',
                                    array('ccn' => 30)
                                )
                            )
                        ),
                    )
                ),
            )
        );

        $mapper = new PdependMapper($reflectionService);
        $classes = $mapper->map($pdepend);

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
        $class = $classes['package1\class1'];
        /* @var $class ClassModel */

        $this->assertEquals('class1', $class->getName());
    }

    /**
     * @depends testMap
     * @param ClassesModel $classes
     */
    public function testClass2(ClassesModel $classes)
    {
        $classes = $classes->getClasses();
        $class = $classes['package2\class2'];
        /* @var $class ClassModel */

        $this->assertEquals('class2', $class->getName());
    }
}
