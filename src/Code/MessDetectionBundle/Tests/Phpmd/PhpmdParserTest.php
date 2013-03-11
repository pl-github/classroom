<?php

namespace Code\MessDetectionBundle\Tests\Phpmd\PhpmdParser;

use Code\MessDetectionBundle\Phpmd\PhpmdParser;
use org\bovigo\vfs\vfsStream;

class PhpcpdParserTest extends \PHPUnit_Framework_TestCase
{
    public function testParse()
    {
        $phpmdXml = <<<EOL
<pmd version="1.4.1" timestamp="2013-03-10T14:53:42+01:00">
  <file name="file1.php">
    <violation beginline="1" endline="2" rule="rule1" ruleset="ruleset1" externalInfoUrl="externalInfoUrl1" priority="1">
      text1
    </violation>
  </file>
  <file name="file2.php">
    <violation beginline="3" endline="4" rule="rule2" ruleset="ruleset2" externalInfoUrl="externalInfoUrl2" priority="2">
      text2
    </violation>
    <violation beginline="5" endline="6" rule="rule3" ruleset="ruleset3" externalInfoUrl="externalInfoUrl3" priority="3">
      text3
    </violation>
  </file>
</pmd>
EOL;

        vfsStream::setup('root', 0777, array('phpmd.xml' => $phpmdXml));

        $parser = new PhpmdParser();

        $pmdCpd = $parser->parse(vfsStream::url('root/phpmd.xml'));

        $this->assertEquals('1.4.1', $pmdCpd->getVersion());
        $this->assertEquals('2013-03-10 14:53:42', $pmdCpd->getTimestamp()->format('Y-m-d H:i:s'));

        return $pmdCpd;
    }

    /**
     * @depends testParse
     */
    public function testFilesFromPmdCpd($pmdCpd)
    {
        $files = $pmdCpd->getFiles();
        $this->assertEquals(2, count($files));

        return $files;
    }

    /**
     * @depends testFilesFromPmdCpd
     */
    public function testFile1FromFiles(array $files)
    {
        $file1 = $files[0];

        $this->assertEquals('file1.php', $file1->getName());

        return $file1;
    }

    /**
     * @depends testFile1FromFiles
     */
    public function testViolationsFromFile1($file1)
    {
        $violations = $file1->getViolations();
        $this->assertEquals(1, count($violations));

        return $violations;
    }

    /**
     * @depends testViolationsFromFile1
     */
    public function testViolation1($violations)
    {
        $violation = $violations[0];

        $this->assertEquals('1', $violation->getBeginLine());
        $this->assertEquals('2', $violation->getEndLine());
        $this->assertEquals('rule1', $violation->getRule());
        $this->assertEquals('ruleset1', $violation->getRuleset());
        $this->assertEquals('externalInfoUrl1', $violation->getExternalInfoUrl());
        $this->assertEquals('1', $violation->getPriority());
        $this->assertEquals('text1', $violation->getText());
    }

    /**
     * @depends testFilesFromPmdCpd
     */
    public function testFile2FromFiles(array $files)
    {
        $file2 = $files[1];

        $this->assertEquals('file2.php', $file2->getName());

        return $file2;
    }

    /**
     * @depends testFile2FromFiles
     */
    public function testViolationsFromFile2($file2)
    {
        $violations = $file2->getViolations();
        $this->assertEquals(2, count($violations));

        return $violations;
    }

    /**
     * @depends testViolationsFromFile2
     */
    public function testViolation2($violations)
    {
        $violation = $violations[0];

        $this->assertEquals('3', $violation->getBeginLine());
        $this->assertEquals('4', $violation->getEndLine());
        $this->assertEquals('rule2', $violation->getRule());
        $this->assertEquals('ruleset2', $violation->getRuleset());
        $this->assertEquals('externalInfoUrl2', $violation->getExternalInfoUrl());
        $this->assertEquals('2', $violation->getPriority());
        $this->assertEquals('text2', $violation->getText());
    }


    /**
     * @depends testViolationsFromFile2
     */
    public function testViolation3($violations)
    {
        $violation = $violations[1];

        $this->assertEquals('5', $violation->getBeginLine());
        $this->assertEquals('6', $violation->getEndLine());
        $this->assertEquals('rule3', $violation->getRule());
        $this->assertEquals('ruleset3', $violation->getRuleset());
        $this->assertEquals('externalInfoUrl3', $violation->getExternalInfoUrl());
        $this->assertEquals('3', $violation->getPriority());
        $this->assertEquals('text3', $violation->getText());
    }
}
