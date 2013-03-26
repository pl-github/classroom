<?php

namespace Code\RepositoryBundle;

use Code\ProjectBundle\DataDir;
use Code\RepositoryBundle\Entity\RepositoryConfig;

class RepositoryFactory
{
    /**
     * Create repository from repository config
     *
     * @param RepositoryConfig $repositoryConfig
     * @param string           $projectDirectory
     * @return RepositoryInterface
     * @throws \RuntimeException
     */
    public function factory(RepositoryConfig $repositoryConfig, DataDir $dataDir)
    {
        $type = $repositoryConfig->getType();

        switch ($type) {
            case 'local':
                $repository = new LocalRepository($repositoryConfig, $dataDir);
                break;
            case 'git':
                $repository = new GitRepository($repositoryConfig, $dataDir);
                break;
            default:
                throw new \RuntimeException('Unknown repository type ' . $type);
        }

        return $repository;
    }
}
