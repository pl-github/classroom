<?php

namespace Code\AnalyzerBundle\Result\Node;

use Code\AnalyzerBundle\Result\Reference\Referencable;

interface NodeInterface extends Referencable
{
    /*+
     * Return name
     *
     * @return string
     */
    public function getName();
}
