<?php

namespace Code\AnalyzerBundle\Model;

class MetricModel
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

    /*+
     * Return key
     *
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /*+
     * Return value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }
}
