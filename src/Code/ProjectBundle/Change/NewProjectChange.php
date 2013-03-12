<?php

namespace Code\ProjectBundle\Change;

class NewProjectChange implements ChangeInterface
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $repositoryType;

    /**
     * @var \DateTime
     */
    private $date;

    /**
     * @param string $id
     * @param string $name
     * @param string $repositoryType
     */
    public function __construct($id, $name, $repositoryType)
    {
        $this->id = $id;
        $this->name = $name;
        $this->repositoryType = $repositoryType;

        $this->date = new \DateTime();
    }

    /**
     * Return id
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Return name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Return repository type
     *
     * @return string
     */
    public function getRepositoryType()
    {
        return $this->repositoryType;
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
