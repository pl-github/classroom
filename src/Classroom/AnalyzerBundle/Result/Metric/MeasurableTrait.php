<?php

namespace Classroom\AnalyzerBundle\Result\Metric;

trait MeasurableTrait
{
    /**
     * @var MetricInterface[]
     */
    private $metrics = [];

    /**
     * Return metrics
     *
     * @return MetricInterface[]
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
     * @param MetricInterface $metric
     * @return $this
     */
    public function addMetric(MetricInterface $metric)
    {
        $this->metrics[$metric->getKey()] = $metric;

        return $this;
    }


    /**
     * Return single metric
     *
     * @param string $key
     * @return MetricInterface|null
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
