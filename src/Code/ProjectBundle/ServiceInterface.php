<?php

namespace Code\ProjectBundle;

interface ServiceInterface
{
    /**
     * Run service
     *
     * @param string $directory
     * @return mixed
     */
    public function run($directory);
}
