<?php

namespace Code\AnalyzerBundle\Result\Metric;

interface MetricInterface
{
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
