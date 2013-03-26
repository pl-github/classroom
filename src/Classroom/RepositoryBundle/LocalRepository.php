<?php

namespace Classroom\RepositoryBundle;

use Classroom\ProjectBundle\DataDir;
use Classroom\RepositoryBundle\Entity\RepositoryConfig;
use Classroom\RepositoryBundle\VersionStrategy\IncrementalVersionStrategy;
use Classroom\RepositoryBundle\VersionStrategy\VersionStrategyInterface;

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
        $this->dataDir = $dataDir;

        $this->versionStrategy = new IncrementalVersionStrategy();
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
