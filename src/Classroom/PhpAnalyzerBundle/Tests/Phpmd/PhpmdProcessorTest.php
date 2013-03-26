<?php

namespace Classroom\PhpAnalyzerBundle\Tests\Phpmd;

use Classroom\AnalyzerBundle\Log\Log;
use Classroom\AnalyzerBundle\Result\Result;
use Classroom\PhpAnalyzerBundle\Node\PhpClassNode;
use Classroom\PhpAnalyzerBundle\Node\PhpFileNode;
use Classroom\PhpAnalyzerBundle\Phpmd\PhpmdProcessor;
use org\bovigo\vfs\vfsStream;

class PhpmdProcessorTest extends \PHPUnit_Framework_TestCase
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

        $collectorMock = $this->getMockBuilder('Classroom\PhpAnalyzerBundle\Phpmd\PhpmdCollector')
            ->disableOriginalConstructor()
            ->getMock();

        $collectorMock
            ->expects($this->once())
            ->method('collect')
            ->will($this->returnValue(vfsStream::url('root/phpmd.xml')));

        $processor = new PhpmdProcessor($collectorMock);

        $result = new Result();
        $result->setLog(new Log());
        $fileNode1 = new PhpFileNode('file1.php');
        $fileNode2 = new PhpFileNode('file2.php');
        $result->addNode($fileNode1);
        $result->addNode($fileNode2);
        $result->addNode(new PhpClassNode('class1'), $fileNode1);
        $result->addNode(new PhpClassNode('class2'), $fileNode2);

        $processor->process($result);

        return $result;
    }

    /**
     * @depends testProcess
     * @param Result $result
     */
    public function testClass1(Result $result)
    {
        $this->assertTrue($result->hasSmells());
    }
}
