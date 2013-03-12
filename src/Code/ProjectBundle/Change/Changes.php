<?php

namespace Code\ProjectBundle\Change;

class Changes
{
    /**
     * @var array
     */
    private $changes = array();

    /**
     * Add change
     *
     * @param ChangeInterface $change
     * @return $this;
     */
    public function addChange(ChangeInterface $change)
    {
        $this->changes[] = $change;
    }

    /**
     * Return items
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

    /**
     * Merge changeSet
     *
     * @param ChangeSet $changeSet
     * @return $this
     */
    public function mergeChangeSet(ChangeSet $changeSet)
    {
        foreach ($changeSet->getChanges() as $change) {
            $this->addChange($change);
        }

        return $this;
    }
}
