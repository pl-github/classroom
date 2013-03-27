<?php

namespace Classroom\AnalyzerBundle\Tests\Serializer;

use Classroom\AnalyzerBundle\Result\Result;
use Classroom\AnalyzerBundle\Result\Smell\Smell;
use Classroom\AnalyzerBundle\Result\Source\Source;
use Classroom\AnalyzerBundle\Result\Source\SourceRange;
use Classroom\AnalyzerBundle\Result\Source\Storage\StringStorage;
use Classroom\AnalyzerBundle\Serializer\XmlSerializer;
use Classroom\PhpAnalyzerBundle\Node\PhpFileNode;
use org\bovigo\vfs\vfsStream;

class XmlSerializerTest extends \PHPUnit_Framework_TestCase
{
    public function testSerialize()
    {
        $serializer = new XmlSerializer();

        $result = new Result();
        $result->setGpa(123);
        $result->setBreakdown(array('A' => 1, 'B' => 2, 'C' => 3, 'D' => 4, 'F' => 5));
        $result->setBuiltAt(new \DateTime('2013-03-25T13:24:52+01:00'));
        $fileNode = new PhpFileNode('file');
        $result->addNode($fileNode);
        $classNode = new PhpFileNode('class');
        $result->addNode($classNode, $fileNode);
        $smell = new Smell('origin', 'rule', 'text', new SourceRange(10, 20), 5);
        $smell->setHash('0b2c4674ea4c715a57d8ca7ab859d0902d5ee3f8');
        $result->addSmell($smell, $classNode);
        $source = new Source(new StringStorage('content'));
        $source->setHash('c02dab12919246ad2f4c22014bdab84a842cb1b6');
        $result->addSource($source, $fileNode);

        $xml = $serializer->serialize($result);

        $expected = <<<EOL
<?xml version="1.0" encoding="UTF-8"?>
<result gpa="123" breakdown="A:1,B:2,C:3,D:4,F:5" builtAt="2013-03-25T13:24:52+01:00">
  <nodes>
    <node class="Classroom\PhpAnalyzerBundle\Node\PhpFileNode" hash="file" name="file"/>
    <node class="Classroom\PhpAnalyzerBundle\Node\PhpFileNode" hash="class" name="class"/>
  </nodes>
  <smells>
    <smell class="Classroom\AnalyzerBundle\Result\Smell\Smell" hash="0b2c4674ea4c715a57d8ca7ab859d0902d5ee3f8" origin="origin" rule="rule" score="5" beginLine="10" endLine="20">text</smell>
  </smells>
  <sources>
    <source hash="c02dab12919246ad2f4c22014bdab84a842cb1b6" class="Classroom\AnalyzerBundle\Result\Source\Source" storageClass="Classroom\AnalyzerBundle\Result\Source\Storage\StringStorage">content</source>
  </sources>
  <references>
    <reference type="node" direction="children" hash="file">
      <referenceHash>class</referenceHash>
    </reference>
    <reference type="node" direction="parent" hash="class">
      <referenceHash>file</referenceHash>
    </reference>
    <reference type="smell" direction="nodeToSmells" hash="class">
      <referenceHash>0b2c4674ea4c715a57d8ca7ab859d0902d5ee3f8</referenceHash>
    </reference>
    <reference type="smell" direction="smellToNode" hash="0b2c4674ea4c715a57d8ca7ab859d0902d5ee3f8">
      <referenceHash>class</referenceHash>
    </reference>
    <reference type="source" direction="nodeToSource" hash="file">
      <referenceHash>c02dab12919246ad2f4c22014bdab84a842cb1b6</referenceHash>
    </reference>
    <reference type="source" direction="sourceToNode" hash="c02dab12919246ad2f4c22014bdab84a842cb1b6">
      <referenceHash>file</referenceHash>
    </reference>
  </references>
</result>
EOL;

        $this->assertEquals(trim($expected), trim($xml));
    }

    public function testDeserialize()
    {
        $serializer = new XmlSerializer();

        $xml = <<<EOL
<?xml version="1.0" encoding="UTF-8"?>
<result gpa="123" breakdown="A:1,B:2,C:3,D:4,F:5" builtAt="2013-03-25T12:32:42+01:00">
  <nodes>
    <node class="Classroom\PhpAnalyzerBundle\Node\PhpFileNode" hash="/src/Classroom/AnalyzerBundle/Grader/GradeCounter.php" name="/src/Classroom/AnalyzerBundle/Grader/GradeCounter.php"/>
    <node class="Classroom\PhpAnalyzerBundle\Node\PhpClassNode" hash="Classroom\AnalyzerBundle\Grader\GradeCounter" name="Classroom\AnalyzerBundle\Grader\GradeCounter" grade="A">
      <metric class="Classroom\AnalyzerBundle\Result\Metric\Metric" key="lines" value="24"/>
      <metric class="Classroom\AnalyzerBundle\Result\Metric\Metric" key="linesOfCode" value="10"/>
      <metric class="Classroom\AnalyzerBundle\Result\Metric\Metric" key="methods" value="1"/>
      <metric class="Classroom\AnalyzerBundle\Result\Metric\Metric" key="linesOfCodePerMethod" value="10"/>
      <metric class="Classroom\AnalyzerBundle\Result\Metric\Metric" key="complexity" value="3"/>
      <metric class="Classroom\AnalyzerBundle\Result\Metric\Metric" key="complexityPerMethod" value="3"/>
    </node>
  </nodes>
  <smells>
    <smell class="Classroom\AnalyzerBundle\Result\Smell\Smell" hash="6d3fc884f340ef89ea3e5ec3d516f27820909944" origin="CodeStyle" rule="CodeStyleError" score="2" beginLine="31" endLine="31"/>
  </smells>
  <sources>
    <source hash="80cd3720ecbf201a6638b27bbc59bb32d704de1d" class="Classroom\AnalyzerBundle\Result\Source\Source" storageClass="Classroom\AnalyzerBundle\Result\Source\Storage\FilesystemStorage">source/80cd3720ecbf201a6638b27bbc59bb32d704de1d.txt</source>
  </sources>
  <references>
    <reference type="node" direction="children" hash="/src/Classroom/AnalyzerBundle/Grader/GradeCounter.php">
      <referenceHash>Classroom\AnalyzerBundle\Grader\GradeCounter</referenceHash>
    </reference>
    <reference type="node" direction="parent" hash="Classroom\AnalyzerBundle\Grader\GradeCounter">
      <referenceHash>/src/Classroom/AnalyzerBundle/Grader/GradeCounter.php</referenceHash>
    </reference>
    <reference type="source" direction="nodeToSource" hash="/src/Classroom/AnalyzerBundle/Grader/GradeCounter.php">
      <referenceHash>80cd3720ecbf201a6638b27bbc59bb32d704de1d</referenceHash>
    </reference>
    <reference type="source" direction="sourceToNode" hash="80cd3720ecbf201a6638b27bbc59bb32d704de1d">
      <referenceHash>/src/Classroom/AnalyzerBundle/Grader/GradeCounter.php</referenceHash>
    </reference>
    <reference type="smell" direction="nodeToSmells" hash="Classroom\AnalyzerBundle\Grader\GradeCounter">
      <referenceHash>6d3fc884f340ef89ea3e5ec3d516f27820909944</referenceHash>
    </reference>
    <reference type="smell" direction="smellToNode" hash="6d3fc884f340ef89ea3e5ec3d516f27820909944">
      <referenceHash>Classroom\AnalyzerBundle\Grader\GradeCounter</referenceHash>
    </reference>
  </references>
</result>
EOL;

        $result = $serializer->deserialize($xml);

        $this->assertEquals(123, $result->getGpa());
        $this->assertEquals(array('A' => 1, 'B' => 2, 'C' => 3, 'D' => 4, 'F' => 5), $result->getBreakdown());

        $fileNode = $result->getNode('/src/Classroom/AnalyzerBundle/Grader/GradeCounter.php');
        $this->assertNotNull($fileNode);

        $classNode = $result->getNode('Classroom\AnalyzerBundle\Grader\GradeCounter');
        $this->assertNotNull($classNode);

        $this->assertTrue($result->hasReference('node', 'parent', $classNode));
        $this->assertTrue($result->hasReference('node', 'children', $fileNode));

        $source = $result->getSource('80cd3720ecbf201a6638b27bbc59bb32d704de1d');
        $this->assertNotNull($source);

        $this->assertTrue($result->hasReference('source', 'sourceToNode', $source));
        $this->assertTrue($result->hasReference('source', 'nodeToSource', $fileNode));

        $smell = $result->getSmell('6d3fc884f340ef89ea3e5ec3d516f27820909944');
        $this->assertNotNull($smell);

        $this->assertTrue($result->hasReference('smell', 'smellToNode', $smell));
        $this->assertTrue($result->hasReference('smell', 'nodeToSmells', $classNode));
    }
}
