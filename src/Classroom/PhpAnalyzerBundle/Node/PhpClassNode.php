<?php

namespace Classroom\PhpAnalyzerBundle\Node;

use Classroom\AnalyzerBundle\Grader\Gradable;
use Classroom\AnalyzerBundle\Grader\GradableTrait;
use Classroom\AnalyzerBundle\Result\Metric\Measurable;
use Classroom\AnalyzerBundle\Result\Metric\MeasurableTrait;
use Classroom\AnalyzerBundle\Result\Metric\MetricInterface;
use Classroom\AnalyzerBundle\Result\Node\NodeInterface;
use Classroom\AnalyzerBundle\Result\Node\NodeReference;
use Classroom\AnalyzerBundle\Result\Node\NodeTrait;

class PhpClassNode implements NodeInterface, Measurable, Gradable
{
    use NodeTrait;
    use MeasurableTrait;
    use GradableTrait;

    /**
     * @param string $name
     */
    public function __construct($name)
    {
        $this->setName($name);
        $this->setHash($name);
    }

}
