<?php


namespace Code\AnalyzerBundle\Tests\Loader;

use Code\AnalyzerBundle\Loader\LoaderResolver;

class LoaderResolverTest extends \PHPUnit_Framework_TestCase
{
    public function testResolveReturnsSupportingWriter()
    {
        $loader1 = $this->getMockBuilder('Code\AnalyzerBundle\Loader\LoaderInterface')
            ->getMock();
        $loader1->expects($this->once())
            ->method('supports')
            ->will($this->returnValue(false));

        $loader2 = $this->getMockBuilder('Code\AnalyzerBundle\Loader\LoaderInterface')
            ->getMock();
        $loader2->expects($this->once())
            ->method('supports')
            ->will($this->returnValue(true));

        $resolver = new LoaderResolver(array($loader1, $loader2));

        $loader = $resolver->resolve('test.ext');

        $this->assertSame($loader2, $loader);
    }

    public function testResolveReturnsFalseOnUnsupported()
    {
        $resolver = new LoaderResolver(array());

        $loader = $resolver->resolve('test.ext');

        $this->assertFalse($loader);
    }
}
