<?php

namespace Code\ProjectBundle;

use Code\ProjectBundle\Feed\Feed;
use Code\RepositoryBundle\RepositoryConfig;

class Project
{
    /**
     * @var mixed
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $libDir;

    /**
     * @var RepositoryConfig
     */
    protected $repositoryConfig;

    /**
     * @var mixed
     */
    protected $latestBuildVersion;

    /**
     * @var mixed
     */
    protected $previousBuildVersion;

    /**
     * @param string $id
     * @param string $name
     * @param string $libDir
     * @param RepositoryConfig $repositoryConfig
     */
    public function __construct($id, $name, $libDir, RepositoryConfig $repositoryConfig)
    {
        $this->id = $id;
        $this->name = $name;
        $this->libDir = $libDir;
        $this->repositoryConfig = $repositoryConfig;

        $this->latestBuildVersion = null;
        $this->previousBuildVersion = null;
    }

    /**
     * Return id
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Return name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Return library directory
     *
     * @return string
     */
    public function getLibDir()
    {
        return $this->libDir;
    }

    /**
     * Return repository config
     *
     * @return RepositoryConfig
     */
    public function getRepositoryConfig()
    {
        return $this->repositoryConfig;
    }

    /**
     * Set latest build version
     *
     * @param mixed $latestBuildVersion
     * @return $this
     */
    public function setLatestBuildVersion($latestBuildVersion)
    {
        $this->previousBuildVersion = $this->latestBuildVersion;

        $this->latestBuildVersion = $latestBuildVersion;

        return $this;
    }

    /**
     * Return latest build version
     *
     * @return mixed|null
     */
    public function getLatestBuildVersion()
    {
        return $this->latestBuildVersion;
    }

    /**
     * Return previous build version
     *
     * @return mixed|null
     */
    public function getPreviousBuildVersion()
    {
        return $this->previousBuildVersion;
    }
}
