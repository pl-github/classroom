<?php

namespace Code\MetricsBundle\Pdepend\Model;

class PackageModel
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var array
     */
    private $metrics = array();

    /**
     * @var array
     */
    private $classes = array();

    /**
     * @param string $name
     * @param array  $metrics
     * @param array  $classes
     */
    public function __construct($name, array $metrics, array $classes = array())
    {
        $this->name = $name;
        $this->metrics = $metrics;
        $this->classes = $classes;
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
     * Return metrics
     *
     * @return array
     */
    public function getMetrics()
    {
        return $this->metrics;
    }

    /**
     * Add class
     *
     * @param ClassModel $class
     * @return $this
     */
    public function addClass(ClassModel $class)
    {
        $this->classes[] = $class;

        return $this;
    }

    /**
     * Return classes
     *
     * @return array
     */
    public function getClasses()
    {
        return $this->classes;
    }
}
