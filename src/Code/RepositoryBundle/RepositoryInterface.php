<?php

namespace Code\RepositoryBundle;

interface RepositoryInterface
{
    /**
     * Return source directory
     *
     * @return string
     */
    public function getSourceDirectory();

    /**
     * Return version strategy
     *
     * @return IncrementalVersionStrategy
     */
    public function getVersionStrategy();
}
