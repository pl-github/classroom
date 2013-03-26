<?php

namespace Classroom\AnalyzerBundle\Result\Node;

use Classroom\AnalyzerBundle\Result\Reference\Referencable;

interface NodeInterface extends Referencable
{
    /*+
     * Return name
     *
     * @return string
     */
    public function getName();
}
