<?php

namespace Code\AnalyzerBundle\Grader;

use Code\AnalyzerBundle\Node\NodeInterface;
use Code\AnalyzerBundle\Smell\SmellInterface;

interface GraderInterface
{
    /**
     * Grade node
     *
     * @param NodeInterface $node
     * @param SmellInterface[] $smells
     */
    public function grade(NodeInterface $node, array $smells);
}
