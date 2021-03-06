<?php

namespace Classroom\RepositoryBundle\VersionStrategy;

use Classroom\ProjectBundle\DataDir;

class IncrementalVersionStrategy implements VersionStrategyInterface
{
    /**
     * @inheritDoc
     */
    public function determineVersion(DataDir $dataDir)
    {
        $versionFilename = $dataDir->getFile('version');

        $version = 0;
        if (file_exists($versionFilename)) {
            $version = (integer)file_get_contents($versionFilename);
        }

        $version++;

        file_put_contents($versionFilename, $version);

        return $version;
    }
}
