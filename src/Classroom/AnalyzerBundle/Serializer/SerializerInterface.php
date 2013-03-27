<?php

namespace Classroom\AnalyzerBundle\Serializer;

use Classroom\AnalyzerBundle\Result\Result;

interface SerializerInterface
{
    /**
     * Serialize result
     *
     * @param Result $result
     */
    public function serialize(Result $result);

    /**
     * Deserialize data into result
     *
     * @param string $data
     * @return Result
     */
    public function deserialize($data);

    /**
     * Return type
     *
     * @return string
     */
    public function getType();
}
