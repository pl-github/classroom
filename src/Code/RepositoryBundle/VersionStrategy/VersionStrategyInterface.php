<?php

namespace Code\RepositoryBundle\VersionStrategy;

use Code\ProjectBundle\DataDir;

interface VersionStrategyInterface
{
    /**
     * Determine version for build
     *
     * @param DataDir $dataDir
     * @return mixed
     */
    public function determineVersion(DataDir $dataDir);
}
