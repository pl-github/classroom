<?php

namespace Classroom\AnalyzerBundle\Grader;

use Classroom\AnalyzerBundle\Result\Node\NodeInterface;
use Classroom\AnalyzerBundle\Result\Smell\SmellInterface;

interface GraderInterface
{
    /**
     * Calculate grade from node and smells
     *
     * @param NodeInterface $node
     * @param SmellInterface[] $smells
     * @return string
     */
    public function grade(NodeInterface $node, array $smells);

    /**
     * Return calculation values
     *
     * @param NodeInterface $node
     * @param SmellInterface[] $smells
     * @return array
     */
    public function getCalculationValues(NodeInterface $node, array $smells);
}
