<?php

namespace Code\ProjectBundle\Tests;

use Code\ProjectBundle\Project;

class ProjectTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructorInjectedValue()
    {
        $project = new Project('id', 'name', 'sourceDir');

        $this->assertEquals('id', $project->getId());
        $this->assertEquals('name', $project->getName());
        $this->assertEquals('sourceDir', $project->getSourceDirectory());
        $this->assertInstanceOf('Code\ProjectBundle\Feed\Feed', $project->getFeed());
    }

    public function testLatestBuildVersionIsEmptyOnInstanciation()
    {
        $project = new Project('id', 'name', 'sourceDir');

        $this->assertNull($project->getLatestBuildVersion());
    }

    public function testPreviousBuildVersionIsEmptyOnInstanciation()
    {
        $project = new Project('id', 'name', 'sourceDir');

        $this->assertNull($project->getLatestBuildVersion());
    }

    public function testSetLatestBuildVersionSetsLatestBuildVersion()
    {
        $project = new Project('id', 'name', 'sourceDir');
        $project->setLatestBuildVersion('latest');

        $this->assertNull($project->getPreviousBuildVersion());
        $this->assertEquals('latest', $project->getLatestBuildVersion());
    }

    public function testSetLatestBuildVersionUpdatedPreviousBuildVersion()
    {
        $project = new Project('id', 'name', 'sourceDir');
        $project->setLatestBuildVersion('previous');
        $project->setLatestBuildVersion('latest');

        $this->assertEquals('previous', $project->getPreviousBuildVersion());
        $this->assertEquals('latest', $project->getLatestBuildVersion());
    }
}
