<?php

namespace Code\AnalyzerBundle\Serializer;

use Code\AnalyzerBundle\Model\NodeInterface;
use Code\AnalyzerBundle\Model\Reference;
use Code\AnalyzerBundle\Model\ResultModel;
use Code\AnalyzerBundle\Model\SmellModel;

interface SerializerInterface
{
    /**
     * Serialize result
     *
     * @param ResultModel $result
     */
    public function serialize(ResultModel $result);

    /**
     * Return type
     *
     * @return string
     */
    public function getType();
}
