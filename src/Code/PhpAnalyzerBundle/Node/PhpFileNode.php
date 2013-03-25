<?php

namespace Code\PhpAnalyzerBundle\Node;

use Code\AnalyzerBundle\Result\Metric\Measurable;
use Code\AnalyzerBundle\Result\Metric\MetricInterface;
use Code\AnalyzerBundle\Result\Node\NodeInterface;

class PhpFileNode implements NodeInterface, Measurable
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
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /*+
     * @inheritDoc
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @inheritDoc
     */
    public function getHash()
    {
        return $this->getName();
    }

    /**
     * @inheritDoc
     */
    public function setHash($hash)
    {
    }

    /**
     * @inheritDoc
     */
    public function getMetrics()
    {
        return $this->metrics;
    }

    /**
     * @inheritDoc
     */
    public function hasMetrics()
    {
        return count($this->metrics) > 0;
    }

    /**
     * @inheritDoc
     */
    public function addMetric(MetricInterface $metric)
    {
        $this->metrics[$metric->getKey()] = $metric;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getMetric($key)
    {
        if (!$this->hasMetric($key)) {
            return null;
        }

        return $this->metrics[$key];
    }

    /**
     * @inheritDoc
     */
    public function hasMetric($key)
    {
        return isset($this->metrics[$key]);
    }
}
