<?php

namespace Classroom\AnalyzerBundle\Result\Node;

use Classroom\AnalyzerBundle\Result\Reference\Referencable;
use Classroom\AnalyzerBundle\Result\Reference\ReferencableTrait;

trait NodeTrait
{
    use ReferencableTrait;

    /**
     * @var string
     */
    private $name;

    /**
     * Return name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }
}
