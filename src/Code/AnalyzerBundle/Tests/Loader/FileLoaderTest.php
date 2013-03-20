<?php

namespace Code\AnalyzerBundle\Tests\Build\Loader;

use Code\AnalyzerBundle\Loader\FileLoader;
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

        $serializerMock = $this->getMockBuilder('Code\AnalyzerBundle\Serializer\SerializerInterface')
            ->getMock();

        $serializerMock
            ->expects($this->once())
            ->method('deserialize')
            ->will($this->returnArgument(0));

        $this->loader = new FileLoader($serializerMock);
    }

    public function tearDown()
    {
        $this->loader = null;
    }

    public function testLoad()
    {
        $result = $this->loader->load(vfsStream::url('root/test.out'));

        $this->assertEquals('test', $result);
    }
}
