<?php

namespace Code\ProjectBundle\Tests\Entity;

use Code\ProjectBundle\Entity\Project;
use Code\RepositoryBundle\Entity\RepositoryConfig;

class ProjectTest extends \PHPUnit_Framework_TestCase
{
    public function testLatestBuildVersionIsEmptyOnInstanciation()
    {
        $project = new Project();

        $this->assertNull($project->getLatestBuildVersion());
    }

    public function testPreviousBuildVersionIsEmptyOnInstanciation()
    {
        $project = new Project();

        $this->assertNull($project->getLatestBuildVersion());
    }

    public function testSetLatestBuildVersionSetsLatestBuildVersion()
    {
        $project = new Project();
        $project->setLatestBuildVersion('latest');

        $this->assertNull($project->getPreviousBuildVersion());
        $this->assertEquals('latest', $project->getLatestBuildVersion());
    }

    public function testSetLatestBuildVersionUpdatedPreviousBuildVersion()
    {
        $project = new Project();
        $project->setLatestBuildVersion('previous');
        $project->setLatestBuildVersion('latest');

        $this->assertEquals('previous', $project->getPreviousBuildVersion());
        $this->assertEquals('latest', $project->getLatestBuildVersion());
    }
}
