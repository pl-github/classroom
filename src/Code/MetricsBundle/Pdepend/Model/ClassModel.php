<?php

namespace Code\MetricsBundle\Pdepend\Model;

class ClassModel
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $file;

    /**
     * @var array
     */
    private $metrics = array();

    /**
     * @var array
     */
    private $methods = array();

    /**
     * @param string $name
     * @param string $file
     * @param array  $metrics
     * @param array  $methods
     */
    public function __construct($name, $file, array $metrics, array $methods = array())
    {
        $this->name = $name;
        $this->file = $file;
        $this->metrics = $metrics;
        $this->methods = $methods;
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
     * Return file
     *
     * @return string
     */
    public function getFile()
    {
        return $this->file;
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
     * Add method
     *
     * @param MethodModel $method
     * @return $this
     */
    public function addMethod(MethodModel $method)
    {
        $this->methods[] = $method;

        return $this;
    }

    /**
     * Return methods
     *
     * @return array
     */
    public function getMethods()
    {
        return $this->methods;
    }
}
