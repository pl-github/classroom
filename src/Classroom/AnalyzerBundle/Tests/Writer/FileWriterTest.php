<?php

namespace Classroom\AnalyzerBundle\Tests\Writer;

use Classroom\AnalyzerBundle\Result\Result;
use Classroom\AnalyzerBundle\Writer\FileWriter;
use org\bovigo\vfs\vfsStream;

class FileWriterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FileWriter
     */
    private $writer;

    public function setUp()
    {
        vfsStream::setup('root', 0777);

        $this->serializerMock = $this->getMockBuilder('Classroom\AnalyzerBundle\Serializer\SerializerInterface')
            ->getMock();

        $this->writer = new FileWriter($this->serializerMock);
    }

    public function tearDown()
    {
        $this->writer = null;
    }

    public function testWrite()
    {
        $result = new Result();

        $this->writer->write($result, vfsStream::url('root/test.out'));

        $this->assertTrue(file_exists(vfsStream::url('root/test.out')));
    }

    public function testSupports()
    {
        $this->serializerMock
            ->expects($this->once())
            ->method('getType')
            ->will($this->returnValue('xml'));

        $result = $this->writer->supports(vfsStream::url('root/test.xml'));

        $this->assertTrue($result);
    }
}
