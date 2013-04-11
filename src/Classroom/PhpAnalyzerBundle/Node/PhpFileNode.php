<?php

namespace Classroom\PhpAnalyzerBundle\Node;

use Classroom\AnalyzerBundle\Result\Metric\Measurable;
use Classroom\AnalyzerBundle\Result\Metric\MeasurableTrait;
use Classroom\AnalyzerBundle\Result\Node\NodeInterface;
use Classroom\AnalyzerBundle\Result\Node\NodeTrait;

class PhpFileNode implements NodeInterface, Measurable
{
    use NodeTrait;
    use MeasurableTrait;

    /**
     * @param string $name
     */
    public function __construct($name)
    {
        $this->setName($name);
        $this->setHash(str_replace('\\', '/', $name));
    }
}
