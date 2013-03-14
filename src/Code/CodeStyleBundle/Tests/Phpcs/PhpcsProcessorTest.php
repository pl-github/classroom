<?php

namespace Code\CopyPasteDetectionBundle\Tests\Phpcs\PhpcsProcessor;

use Code\CodeStyleBundle\Phpcs\PhpcsProcessor;
use org\bovigo\vfs\vfsStream;

class PhpcpdParserTest extends \PHPUnit_Framework_TestCase
{
    public function testProcess()
    {
        $phpcsXml = <<<EOL
<?xml version="1.0" encoding="UTF-8"?>
<phpcs version="1.4.4">
 <file name="file1.php" errors="1" warnings="2">
  <error line="16" column="1" source="source1" severity="5">message1</error>
  <warning line="21" column="1" source="source1" severity="5">message2</warning>
  <warning line="24" column="1" source="source2" severity="5">message3</warning>
 </file>
 <file name="file2.php" errors="0" warnings="2">
  <warning line="15" column="1" source="source3" severity="5">message4</warning>
  <warning line="17" column="1" source="source4" severity="5">message5</warning>
 </file>
 <file name="file3.php" errors="1" warnings="0">
  <error line="15" column="1" source="source5" severity="5">mesage6</error>
 </file>
</phpcs>
EOL;

        vfsStream::setup('root', 0777, array('phpcs.xml' => $phpcsXml));

        $reflectionServiceMock = $this->getMockBuilder('Code\AnalyzerBundle\ReflectionService')
            ->disableOriginalConstructor()
            ->getMock();

        $reflectionServiceMock
            ->expects($this->any())
            ->method('getClassnameForFile')
            ->will($this->returnArgument(0));

        $processor = new PhpcsProcessor($reflectionServiceMock);

        $classes = $processor->process(vfsStream::url('root/phpcs.xml'));

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
        $this->assertSame(3, count($classes));

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
        $this->assertEquals(3, count($class->getSmells()));

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
        $this->assertEquals(2, count($class->getSmells()));
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
}
