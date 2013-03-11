<?php

namespace Code\AnalyzerBundle\Model;

class ClassesModel
{
    /**
     * @var array
     */
    private $classes = array();

    /**
     * @param array $classes
     */
    public function __construct(array $classes = array())
    {
        $this->classes = $classes;
    }

    /**
     * @inheritDoc
     */
    public function getClasses()
    {
        return $this->classes;
    }

    /**
     * Add class
     *
     * @param ClassModel $class
     * @return $this
     */
    public function addClass(ClassModel $class)
    {
        $this->classes[$class->getName()] = $class;

        return $this;
    }

    /**
     * Return class by name
     *
     * @param string $name
     * @return ClassModel
     */
    public function getClass($name)
    {
        if (!$this->hasClass($name)) {
            return null;
        }

        return $this->classes[$name];
    }

    /**
     * Is this class set?
     *
     * @param string $name
     * @return boolean
     */
    public function hasClass($name)
    {
        return !empty($this->classes[$name]);
    }
}
