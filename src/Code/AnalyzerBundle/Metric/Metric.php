<?php

namespace Code\AnalyzerBundle\Metric;

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
     * @param string $name
     */
    public function __construct($key, $value)
    {
        $this->key = $key;
        $this->value = $value;
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
