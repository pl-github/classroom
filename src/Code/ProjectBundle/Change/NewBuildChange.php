<?php

namespace Code\ProjectBundle\Change;

class NewBuildChange implements ChangeInterface
{
    /**
     * @var mixed
     */
    private $version;

    /**
     * @var \DateTime
     */
    private $date;

    /**
     * @param mixed $version
     */
    public function __construct($version)
    {
        $this->version = $version;

        $this->date = new \DateTime();
    }

    /**
     * Return version
     *
     * @return mixed
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @inheritDoc
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @inheritDoc
     */
    public function getType()
    {
        return 'new_build';
    }
}
