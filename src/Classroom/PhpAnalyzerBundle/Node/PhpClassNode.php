<?php

namespace Classroom\PhpAnalyzerBundle\Node;

use Classroom\AnalyzerBundle\Grader\Gradable;
use Classroom\AnalyzerBundle\Result\Metric\Measurable;
use Classroom\AnalyzerBundle\Result\Metric\MetricInterface;
use Classroom\AnalyzerBundle\Result\Node\NodeInterface;
use Classroom\AnalyzerBundle\Result\Node\NodeReference;

class PhpClassNode implements NodeInterface, Measurable, Gradable
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
    private $grade = null;

    /**
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @inheritDoc
     */
    public function getGrade()
    {
        return $this->grade;
    }

    /**
     * @inheritDoc
     */
    public function setGrade($grade)
    {
        $this->grade = $grade;
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
