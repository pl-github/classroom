<?php

namespace Classroom\AnalyzerBundle\Result\Metric;

class Metric implements MetricInterface
{
    /**
     * @var string
     */
    private $key;

    /**
     * @var string
     */
    private $value;

    /**
     * @param string $key
     * @param string $value
     */
    public function __construct($key, $value)
    {
        $this->key = $key;
        $this->value = $value;
    }

    /*+
     * @inheritDoc
     */
    public function getKey()
    {
        return $this->key;
    }

    /*+
     * @inheritDoc
     */
    public function getValue()
    {
        return $this->value;
    }
}
