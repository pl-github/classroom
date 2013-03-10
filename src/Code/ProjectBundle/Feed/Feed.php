<?php

namespace Code\ProjectBundle\Feed;

use Code\ProjectBundle\Project;

class Feed
{
    /**
     * @var Project
     */
    private $project;

    /**
     * @var array
     */
    private $items = array();

    /**
     * Return project
     *
     * @return Project
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * Add item
     *
     * @param Item $item
     */
    public function addItem(Item $item)
    {
        $this->items[] = $item;
    }

    /**
     * Return items
     *
     * @return array
     */
    public function getItems()
    {
        return array_reverse($this->items);
    }
}