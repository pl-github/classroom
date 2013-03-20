<?php

namespace Code\AnalyzerBundle\Tests\Writer;

use Code\AnalyzerBundle\Model\ResultModel;
use Code\AnalyzerBundle\Writer\FileWriter;
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

        $serializerMock = $this->getMockBuilder('Code\AnalyzerBundle\Serializer\SerializerInterface')
            ->getMock();

        $this->writer = new FileWriter($serializerMock);
    }

    public function tearDown()
    {
        $this->writer = null;
    }

    public function testWrite()
    {
        $result = new ResultModel();

        $this->writer->write($result, vfsStream::url('root/test.out'));

        $this->assertTrue(file_exists(vfsStream::url('root/test.out')));
    }
}
