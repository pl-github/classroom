<?php

namespace Code\PhpAnalyzerBundle\Tests\Phpcpd\PhpcpdProcessor;

use Code\AnalyzerBundle\Model\ResultModel;
use Code\PhpAnalyzerBundle\Node\PhpClassNode;
use Code\PhpAnalyzerBundle\Node\PhpFileNode;
use Code\PhpAnalyzerBundle\Phpcpd\PhpcpdProcessor;
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

        $reflectionServiceMock
            ->expects($this->any())
            ->method('getSourceLines')
            ->will($this->returnValue(array('A', 'B', 'C', 'D')));

        $processor = new PhpcpdProcessor($reflectionServiceMock);

        $result = new ResultModel();
        $fileNode1 = new PhpFileNode('file1.php');
        $fileNode2 = new PhpFileNode('file2.php');
        $fileNode3 = new PhpFileNode('file3.php');
        $fileNode4 = new PhpFileNode('file4.php');
        $fileNode5 = new PhpFileNode('file5.php');
        $result->addNode($fileNode1);
        $result->addNode($fileNode2);
        $result->addNode($fileNode3);
        $result->addNode($fileNode4);
        $result->addNode($fileNode5);
        $result->addNode(new PhpClassNode('class1'), $fileNode1);
        $result->addNode(new PhpClassNode('class2'), $fileNode2);
        $result->addNode(new PhpClassNode('class3'), $fileNode3);
        $result->addNode(new PhpClassNode('class4'), $fileNode4);
        $result->addNode(new PhpClassNode('class5'), $fileNode5);

        $processor->process($result, vfsStream::url('root/pmd-cpd.xml'));

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
