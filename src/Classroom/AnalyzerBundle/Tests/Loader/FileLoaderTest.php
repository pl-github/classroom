<?php

namespace Classroom\AnalyzerBundle\Tests\Loader;

use Classroom\AnalyzerBundle\Loader\FileLoader;
use org\bovigo\vfs\vfsStream;

class FileLoaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FileLoader
     */
    private $loader;

    public function setUp()
    {
        vfsStream::setup('root', 0777, array('test.out' => 'test'));

        $this->serializerMock = $this->getMockBuilder('Classroom\AnalyzerBundle\Serializer\SerializerInterface')
            ->getMock();

        $this->loader = new FileLoader($this->serializerMock);
    }

    public function tearDown()
    {
        $this->loader = null;
    }

    public function testLoad()
    {
        $this->serializerMock
            ->expects($this->once())
            ->method('deserialize')
            ->will($this->returnArgument(0));

        $result = $this->loader->load(vfsStream::url('root/test.out'));

        $this->assertEquals('test', $result);
    }

    public function testSupports()
    {
        $this->serializerMock
            ->expects($this->once())
            ->method('getType')
            ->will($this->returnValue('xml'));

        $result = $this->loader->supports(vfsStream::url('root/test.xml'));

        $this->assertTrue($result);
    }
}
