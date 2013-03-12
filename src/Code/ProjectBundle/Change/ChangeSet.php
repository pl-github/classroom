<?php

namespace Code\ProjectBundle\Change;

use Code\ProjectBundle\Build\Build;

class ChangeSet
{
    /**
     * @var Build
     */
    private $build;

    /**
     * @var array
     */
    private $changes = array();

    /**
     * @param Build $build
     */
    public function __construct(Build $build)
    {
        $this->build = $build;
    }

    /**
     * Return build
     *
     * @return Build
     */
    public function getBuild()
    {
        return $this->build;
    }

    /**
     * @param $change
     */
    public function addChange(ChangeInterface $change)
    {
        $this->changes[] = $change;
    }

    /**
     * Return changes
     *
     * @return array
     */
    public function getChanges()
    {
        return $this->changes;
    }

    /**
     * Are changes available?
     *
     * @return boolean
     */
    public function hasChanges()
    {
        return count($this->changes) > 0;
    }
}
