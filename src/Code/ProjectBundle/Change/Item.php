<?php

namespace Code\ProjectBundle\Feed;

use Code\ProjectBundle\Project;

class Item
{
    /**
     * @var Project
     */
    private $text;

    /**
     * @var \DateTime
     */
    private $date;

    /**
     * @param string    $text
     * @param \DateTime $date
     */
    public function __construct($text, \DateTime $date = null)
    {
        $this->text = $text;

        if (null === $date)
        {
            $date = new \DateTime();
        }

        $this->date = $date;
    }

    /**
     * Return text
     *
     * @param string $text
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Return date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }
}