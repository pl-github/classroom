<?php

namespace Code\ProjectBundle\Change;

use Code\AnalyzerBundle\Model\ClassModel;

interface ChangeInterface
{
    /**
     * @return \DateTime
     */
    public function getDate();

    /**
     * Return type of change
     *
     * @return string
     */
    public function getType();
}
