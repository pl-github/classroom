<?php

namespace Code\ProjectBundle\Entity;

use Code\RepositoryBundle\Entity\RepositoryConfig;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name = "code_project")
 */
class Project
{
    /**
     * @var integer
     * @ORM\Id
     * @ORM\GeneratedValue(strategy = "AUTO")
     * @ORM\Column(type = "bigint", options = {"unsigned": true})
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(type = "string")
     */
    protected $key;

    /**
     * @var string
     * @ORM\Column(type = "string")
     */
    protected $name;

    /**
     * @var RepositoryConfig
     * @ORM\OneToOne(targetEntity="Code\RepositoryBundle\Entity\RepositoryConfig")
     * @ORM\JoinColumn(name="repository_config_id", referencedColumnName="id")
     */
    protected $repositoryConfig;

    /**
     * @var mixed
     * @ORM\Column(type = "integer", nullable=true)
     */
    protected $latestBuildVersion;

    /**
     * @var mixed
     * @ORM\Column(type = "integer", nullable=true)
     */
    protected $previousBuildVersion;

    public function __construct()
    {
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
     * Return key
     *
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Set key
     *
     * @param string $key
     * @return $this
     */
    public function setKey($key)
    {
        $this->key = $key;

        return $this;
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
     * Set name
     *
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
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
     * Set repository config
     *
     * @param RepositoryConfig $repositoryConfig
     */
    public function setRepositoryConfig(RepositoryConfig $repositoryConfig)
    {
        $this->repositoryConfig = $repositoryConfig;

        return $this;
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
