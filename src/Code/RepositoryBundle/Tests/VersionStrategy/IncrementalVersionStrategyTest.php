<?php

namespace Code\ProjectBundle\Tests\Build\VersionStrategy;

use Code\ProjectBundle\Project;
use Code\RepositoryBundle\VersionStrategy\IncrementalVersionStrategy;
use org\bovigo\vfs\vfsStream;

class IncrementalVersionStrategyTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateVersionWithNonexistantVersionFile()
    {
        vfsStream::setup('root');

        $project = new Project('testProject', 'Test Project', 'testSourceDir');

        $strategy = new IncrementalVersionStrategy(vfsStream::url('root'));
        $version = $strategy->determineVersion($project);

        $this->assertEquals(1, $version);
    }

    public function testCreateVersionWithExistantVersionFile()
    {
        vfsStream::setup('root', 0777, array('data' => array('testProject' => array('version' => '5'))));

        $project = new Project('testProject', 'Test Project', 'testSourceDir');

        $strategy = new IncrementalVersionStrategy(vfsStream::url('root'));
        $version = $strategy->determineVersion($project);

        $this->assertEquals(6, $version);
    }

    /**
     * @expectedException \Exception
     */
    public function testWriteThrowsExceptionOnFailedMkdir()
    {
        vfsStream::setup('mkdirRoot', 0);

        $project = new Project('testProject', 'Test Project', 'sourceDir');

        $strategy = new IncrementalVersionStrategy(vfsStream::url('mkdirRoot'));
        $strategy->determineVersion($project);
    }

    /**
     * @expectedException \Exception
     */
    public function testWriteThrowsExceptionOnFailedPermissions()
    {
        vfsStream::setup('permRoot', 0777, array('data' => array('testProject' => array())));
        chmod(vfsStream::url('permRoot/data/testProject'), 0);

        $project = new Project('testProject', 'Test Project', 'sourceDir');

        $strategy = new IncrementalVersionStrategy(vfsStream::url('permRoot'));
        $strategy->determineVersion($project);
    }
}