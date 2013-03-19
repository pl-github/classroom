<?php

namespace Code\AnalyzerBundle\Node;

use Code\AnalyzerBundle\Model\Referencable;

interface NodeInterface extends Referencable
{
    /*+
     * Return name
     *
     * @return string
     */
    public function getName();
}
