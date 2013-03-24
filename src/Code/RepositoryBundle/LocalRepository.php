<?php

namespace Code\RepositoryBundle;

use Code\ProjectBundle\DataDir;
use Code\RepositoryBundle\Entity\RepositoryConfig;
use Code\RepositoryBundle\VersionStrategy\IncrementalVersionStrategy;
use Code\RepositoryBundle\VersionStrategy\VersionStrategyInterface;

class LocalRepository implements RepositoryInterface
{
    /**
     * @var string
     */
    private $sourceDirectory;

    /**
     * @var VersionStrategyInterface
     */
    private $versionStrategy;

    /**
     * @var DataDir
     */
    private $dataDir;

    /**
     * @param RepositoryConfig $repositoryConfig
     * @param DataDir          $dataDir
     */
    public function __construct(RepositoryConfig $repositoryConfig, DataDir $dataDir)
    {
        $this->sourceDirectory = $repositoryConfig->getUrl();
        $this->sourceDirectory .= substr($this->sourceDirectory, -1) !== '/' ? '/' : '';
        $this->sourceDirectory .= $repositoryConfig->getLibDir();
        $this->versionStrategy = new IncrementalVersionStrategy();
        $this->dataDir = $dataDir;
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
    public function determineVersion()
    {
        return $this->versionStrategy->determineVersion($this->dataDir);
    }

    /**
     * @inheritDoc
     */
    public function determineBranch()
    {
        return 'master';
    }
}
