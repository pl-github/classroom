<?php

namespace Code\AnalyzerBundle\Metric;

interface MetricInterface
{
    /**
     * Return id
     *
     * @return string
     */
    public function getId();

    /*+
     * Return key
     *
     * @return string
     */
    public function getKey();

    /*+
     * Return value
     *
     * @return string
     */
    public function getValue();
}
