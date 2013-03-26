<?php

namespace Classroom\AnalyzerBundle\Tests\Writer;

use Classroom\AnalyzerBundle\Result\Result;
use Classroom\AnalyzerBundle\Writer\DelegatingWriter;

class DelegatingWriterTest extends \PHPUnit_Framework_TestCase
{
    public function testSupportsResolvesWriter()
    {
        $writerMock = $this->getMockBuilder('Classroom\AnalyzerBundle\Writer\WriterInterface')
            ->getMock();

        $writerResolverMock = $this->getMockBuilder('Classroom\AnalyzerBundle\Writer\WriterResolverInterface')
            ->getMock();
        $writerResolverMock->expects($this->once())
            ->method('resolve')
            ->will($this->returnValue($writerMock));

        $writer = new DelegatingWriter($writerResolverMock);

        $result = $writer->supports('test');

        $this->assertTrue($result);
    }

    public function testWriteResolvesWriter()
    {
        $writerMock = $this->getMockBuilder('Classroom\AnalyzerBundle\Writer\WriterInterface')
            ->getMock();
        $writerMock->expects($this->once())
            ->method('write')
            ->will($this->returnValue('written'));

        $writerResolverMock = $this->getMockBuilder('Classroom\AnalyzerBundle\Writer\WriterResolverInterface')
            ->getMock();
        $writerResolverMock->expects($this->once())
            ->method('resolve')
            ->will($this->returnValue($writerMock));

        $writer = new DelegatingWriter($writerResolverMock);

        $result = new Result();
        $return = $writer->write($result, 'test');

        $this->assertEquals('written', $return);
    }

    /**
     * @expectedException \Exception
     */
    public function testWriteThrowsExceptionOnUnsupported()
    {
        $loaderResolverMock = $this->getMockBuilder('Classroom\AnalyzerBundle\Writer\WriterResolverInterface')
            ->getMock();
        $loaderResolverMock->expects($this->once())
            ->method('resolve')
            ->will($this->returnValue(false));

        $loader = new DelegatingWriter($loaderResolverMock);

        $result = new Result();
        $loader->write($result, 'test');
    }
}
