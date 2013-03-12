<?php

namespace Code\RepositoryBundle;

use Code\RepositoryBundle\VersionStrategy\IncrementalVersionStrategy;

class LocalRepository implements RepositoryInterface
{
    /**
     * @var string
     */
    private $sourceDirectory;

    /**
     * @var string
     */
    private $projectDirectory;

    /**
     * @param RepositoryConfig $repositoryConfig
     * @param string           $projectDirectory
     */
    public function __construct(RepositoryConfig $repositoryConfig, $projectDirectory)
    {
        $this->sourceDirectory = $repositoryConfig->getUrl();
        $this->projectDirectory = $projectDirectory;
    }

    /**
     * @inheritDoc
     */
    public function getSourceDirectory()
    {
        return $this->sourceDirectory;
    }

    /**
     * @inheritDoc
     */
    public function getVersionStrategy()
    {
        return new IncrementalVersionStrategy($this->projectDirectory);
    }
}
