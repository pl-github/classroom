<?php

namespace Code\PhpAnalyzerBundle\Node;

use Code\AnalyzerBundle\Model\MetricModel;
use Code\AnalyzerBundle\Node\NodeInterface;
use Code\AnalyzerBundle\Node\NodeReference;

class PhpClassNode implements NodeInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $namespace;

    /**
     * @var NodeReference
     */
    private $fileReference;

    /**
     * @var array
     */
    private $metrics = array();

    /**
     * @param string        $name
     * @param string        $namespace
     * @param NodeReference $fileReference
     */
    public function __construct($name, $namespace, NodeReference $fileReference)
    {
        $this->name = $name;
        $this->namespace = $namespace;
        $this->fileReference = $fileReference;
    }

    /**
     * @inheritDoc
     */
    public function getId()
    {
        return spl_object_hash($this);
    }

    /*+
     * @inheritDoc
     */
    public function getName()
    {
        return $this->name;
    }

    /*+
     * @inheritDoc
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * @inheritDoc
     */
    public function getFullQualifiedName()
    {
        $name = $this->getName();
        $namespace = $this->getNamespace();

        if ($namespace) {
            $name = $namespace . '\\' . $name;
        }

        return $name;
    }

    /**
     * @inheritDoc
     */
    public function getParentNodeReference()
    {
        return $this->fileReference;
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
     * Is at least one metric set?
     *
     * @return boolean
     */
    public function hasMetrics()
    {
        return count($this->metrics) > 0;
    }

    /**
     * Add metric
     *
     * @param MetricModel $metric
     * @return $this
     */
    public function addMetric(MetricModel $metric)
    {
        $this->metrics[$metric->getKey()] = $metric;

        return $this;
    }

    /**
     * Return single metric
     *
     * @param string $key
     * @return MetricModel
     */
    public function getMetric($key)
    {
        if (!$this->hasMetric($key)) {
            return null;
        }

        return $this->metrics[$key];
    }

    /**
     * Is this metric set?
     *
     * @param string $key
     * @return boolean
     */
    public function hasMetric($key)
    {
        return isset($this->metrics[$key]);
    }
}
