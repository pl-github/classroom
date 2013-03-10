<?php

namespace Code\ProjectBundle;

interface ServiceInterface
{
    /**
     * Run service
     *
     * @param string $sourceDirectory
     * @param string $workDirectory
     * @return mixed
     */
    public function run($sourceDirectory, $workDirectory);
}
