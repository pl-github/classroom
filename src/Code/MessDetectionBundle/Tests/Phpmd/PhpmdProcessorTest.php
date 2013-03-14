<?php

namespace Code\MessDetectionBundle\Tests\Phpmd\PhpmdProcessor;

use Code\AnalyzerBundle\Model\ClassesModel;
use Code\MessDetectionBundle\Phpmd\PhpmdProcessor;
use org\bovigo\vfs\vfsStream;

class PhpcpdProcessorTest extends \PHPUnit_Framework_TestCase
{
    public function testProcess()
    {
        $phpmdXml = <<<EOL
<pmd version="1.4.1" timestamp="2013-03-10T14:53:42+01:00">
  <file name="file1.php">
    <violation beginline="1" endline="2" rule="rule1" ruleset="ruleset1" externalInfoUrl="url1" priority="1">
      text1
    </violation>
  </file>
  <file name="file2.php">
    <violation beginline="3" endline="4" rule="rule2" ruleset="ruleset2" externalInfoUrl="url2" priority="2">
      text2
    </violation>
    <violation beginline="5" endline="6" rule="rule3" ruleset="ruleset3" externalInfoUrl="url3" priority="3">
      text3
    </violation>
  </file>
</pmd>
EOL;

        vfsStream::setup('root', 0777, array('phpmd.xml' => $phpmdXml));

        $reflectionServiceMock = $this->getMockBuilder('Code\AnalyzerBundle\ReflectionService')
            ->disableOriginalConstructor()
            ->getMock();

        $reflectionServiceMock
            ->expects($this->any())
            ->method('getClassnameForFile')
            ->will($this->returnArgument(0));

        $processor = new PhpmdProcessor($reflectionServiceMock);

        $classes = $processor->process(vfsStream::url('root/phpmd.xml'));

        $this->assertEquals(2, count($classes->getClasses()));

        return $classes;
    }

    /**
     * @depends testProcess
     * @param ClassesModel $classes
     */
    public function testClass1(ClassesModel $classes)
    {
        $classes = $classes->getClasses();
        $class = $classes['file1.php'];
        /* @var $class ClassModel */

        $this->assertEquals('file1.php', $class->getName());
        $this->assertEquals(1, count($class->getSmells()));
    }

    /**
     * @depends testProcess
     * @param ClassesModel $classes
     */
    public function testClass2(ClassesModel $classes)
    {
        $classes = $classes->getClasses();
        $class = $classes['file2.php'];
        /* @var $class ClassModel */

        $this->assertEquals('file2.php', $class->getName());
        $this->assertEquals(2, count($class->getSmells()));
    }
}
