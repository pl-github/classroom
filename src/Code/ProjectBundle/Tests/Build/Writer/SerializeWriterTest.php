<?php

namespace Code\ProjectBundle\Tests\Build\Writer;

use Code\ProjectBundle\Build\Build;
use Code\ProjectBundle\Project;
use Code\ProjectBundle\Build\Writer\SerializeWriter;
use Code\RepositoryBundle\RepositoryConfig;
use org\bovigo\vfs\vfsStream;

class SerializeWriterTest extends \PHPUnit_Framework_TestCase
{
    private function createBuild()
    {
        $projectMock = $this->getMockBuilder('\Code\ProjectBundle\Project')
            ->disableOriginalConstructor()
            ->getMock();

        $projectMock->expects($this->once())
            ->method('getId')
            ->will($this->returnValue('testProject'));

        $classesMock = $this->getMockBuilder('\Code\AnalyzerBundle\Model\ClassesModel')
            ->disableOriginalConstructor()
            ->getMock();

        $build = new Build($projectMock, 'testVersion', $classesMock);

        return $build;
    }

    public function testWrite()
    {
        vfsStream::setup('root', 0777);

        $build = $this->createBuild();

        $writer = new SerializeWriter(vfsStream::url('root'));
        $writer->write($build);

        $this->assertTrue(file_exists(vfsStream::url('root/data/testProject/build/testVersion.serialized')));
    }

    /**
     * @expectedException \Exception
     */
    public function testWriteThrowsExceptionOnFailedMkdir()
    {
        vfsStream::setup('mkdirRoot', 0);

        $build = $this->createBuild();

        $writer = new SerializeWriter(vfsStream::url('mkdirRoot'));
        $writer->write($build);
    }

    /**
     * @expectedException \Exception
     */
    public function testWriteThrowsExceptionOnFailedPermissions()
    {
        vfsStream::setup('permRoot', 0777, array('data' => array('testProject' => array('build' => array()))));
        chmod(vfsStream::url('permRoot/data/testProject/build'), 0);

        $build = $this->createBuild();

        $writer = new SerializeWriter(vfsStream::url('permRoot'));
        $writer->write($build);
    }
}
