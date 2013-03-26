<?php

namespace Code\AnalyzerBundle\Tests\Writer;

use Code\AnalyzerBundle\Result\Result;
use Code\AnalyzerBundle\Writer\DelegatingWriter;

class DelegatingWriterTest extends \PHPUnit_Framework_TestCase
{
    public function testSupportsResolvesWriter()
    {
        $writerMock = $this->getMockBuilder('Code\AnalyzerBundle\Writer\WriterInterface')
            ->getMock();

        $writerResolverMock = $this->getMockBuilder('Code\AnalyzerBundle\Writer\WriterResolverInterface')
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
        $writerMock = $this->getMockBuilder('Code\AnalyzerBundle\Writer\WriterInterface')
            ->getMock();
        $writerMock->expects($this->once())
            ->method('write')
            ->will($this->returnValue('written'));

        $writerResolverMock = $this->getMockBuilder('Code\AnalyzerBundle\Writer\WriterResolverInterface')
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
        $loaderResolverMock = $this->getMockBuilder('Code\AnalyzerBundle\Writer\WriterResolverInterface')
            ->getMock();
        $loaderResolverMock->expects($this->once())
            ->method('resolve')
            ->will($this->returnValue(false));

        $loader = new DelegatingWriter($loaderResolverMock);

        $result = new Result();
        $loader->write($result, 'test');
    }
}
