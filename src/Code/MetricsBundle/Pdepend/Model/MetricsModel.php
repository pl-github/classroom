<?php

namespace Code\MetricsBundle\Pdepend\Model;

use Code\AnalyzerBundle\Analyzer\Model\ModelInterface;

class MetricsModel implements ModelInterface
{
    /**
     * @var string
     */
    private $generated;

    /**
     * @var string
     */
    private $pdepend;

    /**
     * @var array
     */
    private $metrics = array();

    /**
     * @var array
     */
    private $packages = array();

    /**
     * @param \DateTime $generated
     * @param string    $pdepend
     * @param array     $metrics
     * @param array     $packages
     */
    public function __construct(\DateTime $generated, $pdepend, array $metrics, array $packages = array())
    {
        $this->generated = $generated;
        $this->pdepend = $pdepend;
        $this->metrics = $metrics;
        $this->packages = $packages;
    }

    /**
     * Return generated
     *
     * @return \DateTIme
     */
    public function getGenerated()
    {
        return $this->generated;
    }

    /**
     * Return pdepend
     *
     * @return string
     */
    public function getPdepend()
    {
        return $this->pdepend;
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
     * Add package
     *
     * @param PackageModel $package
     * @return $this
     */
    public function addPackage(PackageModel $package)
    {
        $this->packages[] = $package;

        return $this;
    }

    /**
     * Return packages
     *
     * @return array
     */
    public function getPackages()
    {
        return $this->packages;
    }
}
