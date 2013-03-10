<?php
/**
 * Created by JetBrains PhpStorm.
 * User: swentz
 * Date: 09.03.13
 * Time: 21:17
 * To change this template use File | Settings | File Templates.
 */

namespace Code\CopyPasteDetectionBundle\Tests\Phpcpd\PhpcpdParser;

use Code\CopyPasteDetectionBundle\Phpcpd\PhpcpdParser;
use org\bovigo\vfs\vfsStream;

class PhpcpdParserTest extends \PHPUnit_Framework_TestCase
{
    public function testParse()
    {
        $pmdCpdXml = <<<EOL
<?xml version="1.0" encoding="UTF-8"?>
<pmd-cpd>
  <duplication lines="5" tokens="15">
    <file path="file1.php" line="10"/>
    <file path="file2.php" line="20"/>
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

        $parser = new PhpcpdParser();

        $pmdCpd = $parser->parse(vfsStream::url('root/pmd-cpd.xml'));

        $duplications = $pmdCpd->getDuplications();
        $this->assertEquals(2, count($duplications));

        $duplication1 = $duplications[0];
        $this->assertEquals(5, $duplication1->getLines());
        $this->assertEquals(15, $duplication1->getTokens());
        $this->assertEquals('exampleCode1', $duplication1->getCodeFragment());

        $files1 = $duplication1->getFiles();
        $this->assertEquals(2, count($files1));

        $file11 = $files1[0];
        $this->assertEquals('file1.php', $file11->getPath());
        $this->assertEquals(10, $file11->getLine());

        $file12 = $files1[1];
        $this->assertEquals('file2.php', $file12->getPath());
        $this->assertEquals(20, $file12->getLine());

        $duplication2 = $duplications[1];
        $this->assertEquals(25, $duplication2->getLines());
        $this->assertEquals(35, $duplication2->getTokens());
        $this->assertEquals('exampleCode2', $duplication2->getCodeFragment());

        $files2 = $duplication2->getFiles();
        $this->assertEquals(3, count($files2));

        $file21 = $files2[0];
        $this->assertEquals('file3.php', $file21->getPath());
        $this->assertEquals(30, $file21->getLine());

        $file22 = $files2[1];
        $this->assertEquals('file4.php', $file22->getPath());
        $this->assertEquals(40, $file22->getLine());

        $file23 = $files2[2];
        $this->assertEquals('file5.php', $file23->getPath());
        $this->assertEquals(50, $file23->getLine());
    }
}
