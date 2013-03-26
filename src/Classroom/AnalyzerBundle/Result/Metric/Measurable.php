<?php

namespace Classroom\AnalyzerBundle\Result\Metric;

interface Measurable
{
    /**
     * Return metrics
     *
     * @return MetricInterface[]
     */
    public function getMetrics();

    /**
     * Is at least one metric set?
     *
     * @return boolean
     */
    public function hasMetrics();

    /**
     * Add metric
     *
     * @param MetricInterface $metric
     * @return $this
     */
    public function addMetric(MetricInterface $metric);

    /**
     * Return single metric
     *
     * @param string $key
     * @return MetricInterface|null
     */
    public function getMetric($key);

    /**
     * Is this metric set?
     *
     * @param string $key
     * @return boolean
     */
    public function hasMetric($key);
}
