<?php

namespace Code\CopyPasteDetectionBundle\Tests\Phpcpd\PhpcpdProcessor;

use Code\CopyPasteDetectionBundle\Phpcpd\PhpcpdProcessor;
use org\bovigo\vfs\vfsStream;

class PhpcpdParserTest extends \PHPUnit_Framework_TestCase
{
    public function testProcess()
    {
        $pmdCpdXml = <<<EOL
<?xml version="1.0" encoding="UTF-8"?>
<pmd-cpd>
  <duplication lines="5" tokens="15">
    <file path="file1.php" line="10"/>
    <file path="file2.php" line="10"/>
    <codefragment>exampleCode1</codefragment>
  </duplication>
  <duplication lines="25" tokens="35">
    <file path="file3.php" line="30"/>
    <file path="file4.php" line="40"/>
    <file path="file5.php" line="50"/>
    <codefragment>exampleCode2</codefragment>
  </duplication>
</pmd-cpd>
EOL;

        vfsStream::setup('root', 0777, array('pmd-cpd.xml' => $pmdCpdXml));

        $reflectionServiceMock = $this->getMockBuilder('Code\AnalyzerBundle\ReflectionService')
            ->disableOriginalConstructor()
            ->getMock();

        $reflectionServiceMock
            ->expects($this->any())
            ->method('getClassnameForFile')
            ->will($this->returnArgument(0));

        $processor = new PhpcpdProcessor($reflectionServiceMock);

        $classes = $processor->process(vfsStream::url('root/pmd-cpd.xml'));

        $this->assertInstanceOf('Code\AnalyzerBundle\Model\ClassesModel', $classes);

        return $classes;
    }

    /**
     * @depends testProcess
     * @param ClassesModel $result
     */
    public function testClasses($result)
    {
        $classes = $result->getClasses();
        $this->assertSame(5, count($classes));

        return $classes;
    }

    /**
     * @depends testClasses
     * @param array $classes
     */
    public function testClass1(array $classes)
    {
        $class = $classes['file1.php'];
        $this->assertInstanceOf('Code\AnalyzerBundle\Model\ClassModel', $class);
        $this->assertEquals(1, count($class->getSmells()));

        return $class;
    }

    /**
     * @depends testClasses
     * @param array $classes
     */
    public function testClass2(array $classes)
    {
        $class = $classes['file2.php'];

        $this->assertInstanceOf('Code\AnalyzerBundle\Model\ClassModel', $class);
        $this->assertEquals(1, count($class->getSmells()));
    }

    /**
     * @depends testClasses
     * @param array $classes
     */
    public function testClass3(array $classes)
    {
        $class = $classes['file3.php'];

        $this->assertInstanceOf('Code\AnalyzerBundle\Model\ClassModel', $class);
        $this->assertEquals(1, count($class->getSmells()));
    }

    /**
     * @depends testClasses
     * @param array $classes
     */
    public function testClass4(array $classes)
    {
        $class = $classes['file4.php'];

        $this->assertInstanceOf('Code\AnalyzerBundle\Model\ClassModel', $class);
        $this->assertEquals(1, count($class->getSmells()));
    }

    /**
     * @depends testClasses
     * @param array $classes
     */
    public function testClass5(array $classes)
    {
        $class = $classes['file5.php'];

        $this->assertInstanceOf('Code\AnalyzerBundle\Model\ClassModel', $class);
        $this->assertEquals(1, count($class->getSmells()));
    }
}
