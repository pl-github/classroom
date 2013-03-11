<?php
/**
 * Created by JetBrains PhpStorm.
 * User: swentz
 * Date: 09.03.13
 * Time: 21:17
 * To change this template use File | Settings | File Templates.
 */

namespace Code\MessDetectionBundle\Tests\Phpmd\PhpmdParser;

use Code\MessDetectionBundle\Phpmd\PhpmdParser;
use org\bovigo\vfs\vfsStream;

class PhpcpdParserTest extends \PHPUnit_Framework_TestCase
{
    public function testParse()
    {
        $phpmdXml = <<<EOL
<pmd version="1.4.1" timestamp="2013-03-10T14:53:42+01:00">
  <file name="/opt/www/code/symfony/src/Code/CopyPasteDetectionBundle/DependencyInjection/CodeCopyPasteDetectionExtension.php">
    <violation beginline="18" endline="18" rule="UnusedFormalParameter" ruleset="Unused Code Rules" externalInfoUrl="http://phpmd.org/rules/unusedcode.html#unusedformalparameter" priority="3">
      Avoid unused parameters such as '\$configs'.
    </violation>
  </file>
  <file name="/opt/www/code/symfony/src/Code/CopyPasteDetectionBundle/Phpcpd/PhpcpdParser.php">
    <violation beginline="24" endline="24" rule="LongVariable" ruleset="Naming Rules" externalInfoUrl="http://phpmd.org/rules/naming.html#longvariable" priority="3">
      Avoid excessively long variable names like \$duplicationAttributes. Keep variable name length under 20.
    </violation>
    <violation beginline="32" endline="48" rule="LongVariable" ruleset="Naming Rules" externalInfoUrl="http://phpmd.org/rules/naming.html#longvariable" priority="3">
      Avoid excessively long variable names like \$duplicationAttributes. Keep variable name length under 20.
    </violation>
  </file>
</pmd>
EOL;

        vfsStream::setup('root', 0777, array('phpmd.xml' => $phpmdXml));

        $parser = new PhpmdParser();

        $pmdCpd = $parser->parse(vfsStream::url('root/phpmd.xml'));

        $this->assertEquals('1.4.1', $pmdCpd->getVersion());
        $this->assertEquals('2013-03-10 14:53:42', $pmdCpd->getTimestamp()->format('Y-m-d H:i:s'));

        $files = $pmdCpd->getFiles();
        $this->assertEquals(2, count($files));

        return $pmdCpd;
    }

    /**
     * @depends testParse
     */
    public function testFiles($pmdCpd)
    {
        $files = $pmdCpd->getFiles();

        $file1 = $files[0];
        $violations1 = $file1->getViolations();
        $this->assertEquals(1, count($violations1));

        $file2 = $files[1];
        $violations2 = $file2->getViolations();
        $this->assertEquals(2, count($violations2));
    }
}
