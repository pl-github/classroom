<?php

namespace Code\RepositoryBundle\VersionStrategy;

use Code\RepositoryBundle\Driver\DriverInterface;

class GitVersionStrategy implements VersionStrategyInterface
{
    /**
     * @var DriverInterface
     */
    private $driver;

    /**
     * @var string
     */
    private $projectDirectory;

    /**
     * @param DriverInterface $driver
     * @param string          $projectDirectory
     */
    public function __construct(DriverInterface $driver, $projectDirectory)
    {
        $this->driver = $driver;
        $this->projectDirectory = $projectDirectory;
    }

    /**
     * @inheritDoc
     */
    public function determineVersion()
    {
        return $this->driver->getLastCommit($this->projectDirectory . '/git');
    }
}
