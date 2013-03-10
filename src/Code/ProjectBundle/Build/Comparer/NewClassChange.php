<?php

namespace Code\ProjectBundle\Build\Comparer;

use Code\ProjectBundle\Model\ClassModel;

class NewClassChange implements ChangeInterface
{
    /**
     * @var ClassModel
     */
    private $class;

    /**
     * @param ClassModel $class
     */
    public function __construct(ClassModel $class)
    {
        $this->class = $class;
    }

    /**
     * @inheritDoc
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @inheritDoc
     */
    public function getText()
    {
        return 'New class';
    }
}