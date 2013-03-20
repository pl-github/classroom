<?php

namespace Code\PhpAnalyzerBundle\Tests\Phpcs;

use Code\AnalyzerBundle\Model\ResultModel;
use Code\PhpAnalyzerBundle\Node\PhpClassNode;
use Code\PhpAnalyzerBundle\Node\PhpFileNode;
use Code\PhpAnalyzerBundle\Phpcs\PhpcsProcessor;
use org\bovigo\vfs\vfsStream;

class PhpcsParserTest extends \PHPUnit_Framework_TestCase
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

        $reflectionServiceMock
            ->expects($this->any())
            ->method('getSourceLines')
            ->will($this->returnValue(array('A', 'B', 'C', 'D')));

        $processor = new PhpcsProcessor($reflectionServiceMock);

        $result = new ResultModel();
        $fileNode1 = new PhpFileNode('file1.php');
        $fileNode2 = new PhpFileNode('file2.php');
        $result->addNode($fileNode1);
        $result->addNode($fileNode2);
        $result->addNode(new PhpClassNode('class1'), $fileNode1);
        $result->addNode(new PhpClassNode('class2'), $fileNode2);

        $processor->process($result, vfsStream::url('root/phpcs.xml'));

        return $result;
    }

    /**
     * @depends testProcess
     * @param ResultModel $result
     */
    public function testClasses(ResultModel $result)
    {
        $this->assertTrue($result->hasSmells());
    }
}
