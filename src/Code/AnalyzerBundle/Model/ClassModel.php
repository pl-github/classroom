<?php

namespace Code\AnalyzerBundle\Model;

class ClassModel
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
     * @var array
     */
    private $smells = array();

    /**
     * @var array
     */
    private $metrics = array();

    /**
     * @param string $name
     */
    public function __construct($name, $namespace = '', array $smells = array(), $metrics = array())
    {
        $this->name = $name;
        $this->namespace = $namespace;
        $this->smells = $smells;
        $this->metrics = $metrics;
    }

    /**
     * Clone
     */
    public function __clone()
    {
        $this->smells = array();
        $this->metrics = array();
    }

    /**
     * Return id
     *
     * @return string
     */
    public function getId()
    {
        return spl_object_hash($this);
    }

    /*+
     * Return name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Return namespace
     *
     * @return string
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * Return full qualified name
     *
     * @return string
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
     * Add smell
     *
     * @param SmellModel $smell
     * @return $this
     */
    public function addSmell(SmellModel $smell)
    {
        $this->smells[] = $smell;

        return $this;
    }

    /*+
     * Return smells
     *
     * @return object
     */
    public function getSmells()
    {
        return $this->smells;
    }

    /**
     * Is at least one smell set?
     *
     * @return boolean
     */
    public function hasSmells()
    {
        return count($this->smells) > 0;
    }

    /*+
     * Return score
     *
     * @return integer
     */
    public function getScore()
    {
        if (!$this->hasSmells()) {
            return 0;

        }
        $score = 0;
        foreach ($this->smells as $smell) {
            $score += $smell->getScore();
        }

        if (!$this->hasMetric('linesOfCode')) {
            return 0;
        }

        $linesOfCode = $this->getMetric('linesOfCode')->getValue();
        if ($linesOfCode) {
            $score /= $linesOfCode;
        }

        return $score;
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
