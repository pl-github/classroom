<?php

namespace Code\PhpAnalyzerBundle\Node;

use Code\AnalyzerBundle\Model\MetricModel;
use Code\AnalyzerBundle\Node\NodeInterface;

class PhpFileNode implements NodeInterface
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
     * @var string
     */
    private $sourceFilename;

    /**
     * @param string $name
     * @param string $sourceFilename
     * @param array  $metrics
     */
    public function __construct($name, array $metrics = array())
    {
        $this->name = $name;
        $this->metrics = $metrics;
    }

    /**
     * Return id
     *
     * @return string
     */
    public function getId()
    {
        return $this->name;
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
    public function getFullQualifiedName()
    {
        $name = $this->getName();

        return $name;
    }

    /**
     * @inheritDoc
     */
    public function getParentNodeReference()
    {
        return null;
    }

    public function setSourceFilename($sourceFilename)
    {
        $this->sourceFilename = $sourceFilename;
    }

    public function getSourceFilename()
    {
        return $this->sourceFilename;
    }

    public function getContent()
    {
        return file_get_contents($this->sourceFilename);
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
