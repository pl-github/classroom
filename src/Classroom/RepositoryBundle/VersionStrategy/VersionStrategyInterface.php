<?php

namespace Classroom\RepositoryBundle\VersionStrategy;

use Classroom\ProjectBundle\DataDir;

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
