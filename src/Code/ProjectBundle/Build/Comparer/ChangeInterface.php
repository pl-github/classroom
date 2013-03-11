<?php

namespace Code\ProjectBundle\Build\Comparer;

use Code\AnalyzerBundle\Model\ClassModel;

interface ChangeInterface
{
    /**
     * Return class
     *
     * @return ClassModel
     */
    public function getClass();

    /**
     * Return text
     *
     * @return string
     */
    public function getText();
}