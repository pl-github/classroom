<?php

namespace Classroom\AnalyzerBundle\Tests\Loader;

use Classroom\AnalyzerBundle\Result\Result;
use Classroom\AnalyzerBundle\Loader\DelegatingLoader;

class DelegatingLoaderTest extends \PHPUnit_Framework_TestCase
{
    public function testSupportsResolvesLoader()
    {
        $loaderMock = $this->getMockBuilder('Classroom\AnalyzerBundle\Loader\LoaderInterface')
            ->getMock();

        $loaderResolverMock = $this->getMockBuilder('Classroom\AnalyzerBundle\Loader\LoaderResolverInterface')
            ->getMock();
        $loaderResolverMock->expects($this->once())
            ->method('resolve')
            ->will($this->returnValue($loaderMock));

        $loader = new DelegatingLoader($loaderResolverMock);

        $result = $loader->supports('test');

        $this->assertTrue($result);
    }

    public function testLoadResolvesLoader()
    {
        $loaderMock = $this->getMockBuilder('Classroom\AnalyzerBundle\Loader\LoaderInterface')
            ->getMock();
        $loaderMock->expects($this->once())
            ->method('load')
            ->will($this->returnValue('written'));

        $loaderResolverMock = $this->getMockBuilder('Classroom\AnalyzerBundle\Loader\LoaderResolverInterface')
            ->getMock();
        $loaderResolverMock->expects($this->once())
            ->method('resolve')
            ->will($this->returnValue($loaderMock));

        $loader = new DelegatingLoader($loaderResolverMock);

        $return = $loader->load('test');

        $this->assertEquals('written', $return);
    }

    /**
     * @expectedException \Exception
     */
    public function testLoadThrowsExceptionOnUnsupported()
    {
        $loaderResolverMock = $this->getMockBuilder('Classroom\AnalyzerBundle\Loader\LoaderResolverInterface')
            ->getMock();
        $loaderResolverMock->expects($this->once())
            ->method('resolve')
            ->will($this->returnValue(false));

        $loader = new DelegatingLoader($loaderResolverMock);

        $loader->load('test');
    }
}
