<?php

namespace Code\ProjectBundle\Tests\Writer;

use Code\ProjectBundle\Project;
use Code\ProjectBundle\Writer\SerializeWriter;
use org\bovigo\vfs\vfsStream;

class SerializeWriterTest extends \PHPUnit_Framework_TestCase
{
    private function createProjectMock()
    {
        $projectMock = $this->getMockBuilder('\Code\ProjectBundle\Project')
            ->disableOriginalConstructor()
            ->getMock();

        $projectMock->expects($this->once())
            ->method('getId')
            ->will($this->returnValue('testProject'));

        return $projectMock;
    }

    public function testWrite()
    {
        vfsStream::setup('root', 0777);

        $project = $this->createProjectMock();

        $writer = new SerializeWriter(vfsStream::url('root'));
        $writer->write($project);

        $this->assertTrue(file_exists(vfsStream::url('root/data/testProject/project.serialized')));
    }

    /**
     * @expectedException \Exception
     */
    public function testWriteThrowsExceptionOnFailedMkdir()
    {
        vfsStream::setup('mkdirRoot', 0);

        $project = $this->createProjectMock();

        $writer = new SerializeWriter(vfsStream::url('mkdirRoot'));
        $writer->write($project);
    }

    /**
     * @expectedException \Exception
     */
    public function testWriteThrowsExceptionOnFailedPermissions()
    {
        vfsStream::setup('permRoot', 0777, array('data' => array('testProject' => array())));
        chmod(vfsStream::url('permRoot/data/testProject'), 0);

        $project = new Project('testProject', 'Test Project', 'sourceDir');

        $writer = new SerializeWriter(vfsStream::url('permRoot'));
        $writer->write($project);
    }
}
