<?php

namespace Code\RepositoryBundle\VersionStrategy;

interface VersionStrategyInterface
{
    /**
     * Determine version for build
     *
     * @return mixed
     */
    public function determineVersion();
}
