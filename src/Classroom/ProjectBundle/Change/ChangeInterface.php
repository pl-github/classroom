<?php

namespace Classroom\ProjectBundle\Change;

use Classroom\AnalyzerBundle\Model\ClassModel;

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
