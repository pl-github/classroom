<?php

namespace Code\AnalyzerBundle\Serializer;

use Code\AnalyzerBundle\Model\ResultModel;

interface SerializerInterface
{
    /**
     * Serialize result
     *
     * @param ResultModel $result
     */
    public function serialize(ResultModel $result);

    /**
     * Deserialize data into result
     *
     * @param string $data
     */
    public function deserialize($data);

    /**
     * Return type
     *
     * @return string
     */
    public function getType();
}
