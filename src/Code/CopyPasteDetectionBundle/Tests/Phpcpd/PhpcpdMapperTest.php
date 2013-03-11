<?php
/**
 * Created by JetBrains PhpStorm.
 * User: swentz
 * Date: 09.03.13
 * Time: 21:17
 * To change this template use File | Settings | File Templates.
 */

namespace Code\CopyPasteDetectionBundle\Tests\Phpcpd\PhpcpdParser;

use Code\AnalyzerBundle\Model\ClassesModel;
use Code\AnalyzerBundle\Model\ClassModel;
use Code\CopyPasteDetectionBundle\Phpcpd\Model\DuplicationModel;
use Code\CopyPasteDetectionBundle\Phpcpd\Model\FileModel;
use Code\CopyPasteDetectionBundle\Phpcpd\Model\PmdCpdModel;
use Code\CopyPasteDetectionBundle\Phpcpd\PhpcpdMapper;
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

        $file1 = new FileModel('testPath1', 10);
        $file2 = new FileModel('testPath2', 20);
        $duplication1 = new DuplicationModel(30, 40, 'codeFragment');
        $duplication1->addFile($file1);
        $duplication1->addFile($file2);
        $pmdCpd = new PmdCpdModel();
        $pmdCpd->addDuplication($duplication1);

        $mapper = new PhpcpdMapper($reflectionServiceMock);

        $result = $mapper->map($pmdCpd);

        $this->assertInstanceOf('Code\AnalyzerBundle\Model\ClassesModel', $result);
        return $result;
    }

    /**
     * @depends testMap
     * @param ClassesModel $result
     */
    public function testClasses($result)
    {
        $classes = $result->getClasses();
        $this->assertSame(2, count($classes));

        return $classes;
    }

    /**
     * @depends testClasses
     * @param array $classes
     */
    public function testClass1(array $classes)
    {
        $class = $classes['testPath1'];
        $this->assertInstanceOf('Code\AnalyzerBundle\Model\ClassModel', $class);

        return $class;
    }

    /**
     * @depends testClasses
     * @param array $classes
     */
    public function testClass2(array $classes)
    {
        $class = $classes['testPath2'];
        $this->assertInstanceOf('Code\AnalyzerBundle\Model\ClassModel', $class);
    }
}
