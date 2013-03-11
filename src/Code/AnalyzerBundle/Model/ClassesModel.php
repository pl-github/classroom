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
        foreach ($classes as $class) {
            $this->addClass($class);
        }
    }

    /**
     * Return id
     *
     * @return string
     */
    public function getId()
    {
        return spl_object_hash($this);
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
        $this->classes[$class->getFullQualifiedName()] = $class;

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
