<?php

namespace Code\RepositoryBundle;

use Code\RepositoryBundle\Driver\GitDriver;

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
    public function factory(RepositoryConfig $repositoryConfig, $projectDirectory)
    {
        $type = $repositoryConfig->getType();

        switch ($type) {
            case 'local':
                $repository = new LocalRepository($repositoryConfig, $projectDirectory);
                break;
            case 'git':
                switch ($type) {
                    case 'git':
                        $driver = new GitDriver($repositoryConfig);
                        break;
                    default:
                        throw new \RuntimeException('Unknown driver type ' . $type);
                }

                $repository = new VcsRepository($repositoryConfig, $driver, $projectDirectory);
                break;
            default:
                throw new \RuntimeException('Unknown repository type ' . $type);
        }

        return $repository;
    }
}
