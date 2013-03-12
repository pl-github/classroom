<?php

namespace Code\AnalyzerBundle\Analyzer\Mapper;

use Code\AnalyzerBundle\Analyzer\Model\ModelInterface;
use Code\AnalyzerBundle\Model\ClassesModel;

interface MapperInterface
{
    /**
     * Map analyzed models to class models
     *
     * @param ModelInterface $model
     * @return ClassesModel
     */
    public function map(ModelInterface $model);
}
