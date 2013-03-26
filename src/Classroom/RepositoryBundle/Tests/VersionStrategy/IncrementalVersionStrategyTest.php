<?php

namespace Classroom\RepositoryBundle\Tests\Build\VersionStrategy;

use Classroom\ProjectBundle\DataDir;
use Classroom\RepositoryBundle\VersionStrategy\IncrementalVersionStrategy;
use org\bovigo\vfs\vfsStream;

class IncrementalVersionStrategyTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateVersionWithNonexistantVersionFile()
    {
        vfsStream::setup('root');

        $dataDir = new DataDir(vfsStream::url('root'));
        $strategy = new IncrementalVersionStrategy();
        $version = $strategy->determineVersion($dataDir);

        $this->assertEquals(1, $version);
    }

    public function testCreateVersionWithExistantVersionFile()
    {
        vfsStream::setup('root', 0777, array('version' => '5'));

        $dataDir = new DataDir(vfsStream::url('root'));
        $strategy = new IncrementalVersionStrategy();
        $version = $strategy->determineVersion($dataDir);

        $this->assertEquals(6, $version);
    }
}
