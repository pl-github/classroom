<?php

namespace Classroom\RepositoryBundle;

interface RepositoryInterface
{
    /**
     * Return source directory
     *
     * @return string
     */
    public function getSourceDirectory();

    /**
     * Determine version
     *
     * @return mixed
     */
    public function determineVersion();

    /**
     * Determine branch
     *
     * @return mixed
     */
    public function determineBranch();
}
