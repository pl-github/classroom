<?php

namespace Code\ProjectBundle\Change;

use Code\AnalyzerBundle\Model\ClassModel;

class ClassRemovedChange implements ChangeInterface
{
    /**
     * @var string
     */
    private $className;

    /**
     * @var \DateTime
     */
    private $date;

    /**
     * @param string $className
     */
    public function __construct($className)
    {
        $this->className = $className;

        $this->date = new \DateTime();
    }

    /**
     * @inheritDoc
     */
    public function getClassName()
    {
        return $this->className;
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
        return 'class_removed';
    }
}
