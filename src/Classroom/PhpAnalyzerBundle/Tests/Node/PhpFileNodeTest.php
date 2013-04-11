<?php

namespace Classroom\PhpAnalyzerBundle\Tests\Node;

use Classroom\PhpAnalyzerBundle\Node\PhpFileNode;

/**
 * @covers Classroom\PhpAnalyzerBundle\Node\PhpFileNode
 */
class PhpFileNodeTest extends \PHPUnit_Framework_TestCase
{
    public function testPathIsUsedAsHash()
    {
        $path = '/tmp/a/path';

        $node = new PhpFileNode($path);

        $this->assertSame($node->getHash(), $path);
    }

    public function testBackslashesAreReplacedWithSlashesInHash()
    {
        $node = new PhpFileNode('D:\\test');

        $this->assertSame($node->getHash(), 'D:/test');
    }

    public function testWindowsDriveLetterIsConvertedToUppercaseInHash()
    {
        $node = new PhpFileNode('d:/test');

        $this->assertSame($node->getHash(), 'D:/test');
    }
}
