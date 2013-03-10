<?php

namespace Code\ProjectBundle;

use Code\ProjectBundle\Feed\Feed;

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
    protected $sourceDirectory;

    /**
     * @var Feed
     */
    protected $feed;

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
     * @param string $sourceDirectory
     */
    public function __construct($id, $name, $sourceDirectory)
    {
        $this->id = $id;
        $this->name = $name;
        $this->sourceDirectory = $sourceDirectory;

        $this->feed = new Feed();

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
     * Return source directory
     *
     * @return string
     */
    public function getSourceDirectory()
    {
        return $this->sourceDirectory;
    }

    /**
     * Return feed
     *
     * @return Feed
     */
    public function getFeed()
    {
        return $this->feed;
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