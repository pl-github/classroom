<?php

namespace Code\RepositoryBundle;

use Code\RepositoryBundle\Driver\DriverInterface;
use Code\RepositoryBundle\VersionStrategy\GitVersionStrategy;

class VcsRepository implements RepositoryInterface
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
     * @param DriverInterface  $driver
     * @param string           $projectDirectory
     */
    public function __construct($repositoryConfig, DriverInterface $driver, $projectDirectory)
    {
        $this->driver = $driver;
        $this->projectDirectory = $projectDirectory;
    }

    /**
     * @inheritDoc
     */
    public function getSourceDirectory()
    {
        $checkoutDirectory = $this->projectDirectory . '/git';

        $this->driver->checkout($checkoutDirectory);

        return $checkoutDirectory;
    }

    /**
     * @inheritDoc
     */
    public function getVersionStrategy()
    {
        return new GitVersionStrategy($this->driver, $this->projectDirectory);
    }
}
