<?php


namespace Classroom\AnalyzerBundle\Tests\Writer;

use Classroom\AnalyzerBundle\Writer\WriterResolver;

class WriterResolverTest extends \PHPUnit_Framework_TestCase
{
    public function testResolveReturnsSupportingWriter()
    {
        $writer1 = $this->getMockBuilder('Classroom\AnalyzerBundle\Writer\WriterInterface')
            ->getMock();

        $writer2 = $this->getMockBuilder('Classroom\AnalyzerBundle\Writer\WriterInterface')
            ->getMock();

        $writer1->expects($this->once())
            ->method('supports')
            ->will($this->returnValue(false));

        $writer2->expects($this->once())
            ->method('supports')
            ->will($this->returnValue(true));

        $resolver = new WriterResolver(array($writer1, $writer2));

        $writer = $resolver->resolve('test.ext');

        $this->assertSame($writer2, $writer);
    }

    public function testResolveReturnsFalseOnUnsupported()
    {
        $resolver = new WriterResolver(array());

        $loader = $resolver->resolve('test.ext');

        $this->assertFalse($loader);
    }
}
