<?php

namespace Code\ProjectBundle\Tests;

use Code\ProjectBundle\Project;
use Code\RepositoryBundle\RepositoryConfig;

class ProjectTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructorInjectedValues()
    {
        $project = new Project('id', 'name', '/lib', new RepositoryConfig('type', 'url'));

        $this->assertEquals('id', $project->getId());
        $this->assertEquals('name', $project->getName());
        $this->assertEquals('/lib', $project->getLibDir());
        $this->assertInstanceOf('Code\RepositoryBundle\RepositoryConfig', $project->getRepositoryConfig());
    }

    public function testLatestBuildVersionIsEmptyOnInstanciation()
    {
        $project = new Project('id', 'name', '/lib', new RepositoryConfig('type', 'url'));

        $this->assertNull($project->getLatestBuildVersion());
    }

    public function testPreviousBuildVersionIsEmptyOnInstanciation()
    {
        $project = new Project('id', 'name', '/lib', new RepositoryConfig('type', 'url'));

        $this->assertNull($project->getLatestBuildVersion());
    }

    public function testSetLatestBuildVersionSetsLatestBuildVersion()
    {
        $project = new Project('id', 'name', '/lib', new RepositoryConfig('type', 'url'));
        $project->setLatestBuildVersion('latest');

        $this->assertNull($project->getPreviousBuildVersion());
        $this->assertEquals('latest', $project->getLatestBuildVersion());
    }

    public function testSetLatestBuildVersionUpdatedPreviousBuildVersion()
    {
        $project = new Project('id', 'name', '/lib', new RepositoryConfig('type', 'url'));
        $project->setLatestBuildVersion('previous');
        $project->setLatestBuildVersion('latest');

        $this->assertEquals('previous', $project->getPreviousBuildVersion());
        $this->assertEquals('latest', $project->getLatestBuildVersion());
    }
}
