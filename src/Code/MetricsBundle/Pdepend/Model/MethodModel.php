<?php

namespace Code\MetricsBundle\Pdepend\Model;

class MethodModel
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
     * @param string $name
     * @param array  $metrics
     */
    public function __construct($name, array $metrics)
    {
        $this->name = $name;
        $this->metrics = $metrics;
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
}
