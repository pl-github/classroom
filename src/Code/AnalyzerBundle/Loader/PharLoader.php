<?php

namespace Code\AnalyzerBundle\Loader;

use Code\AnalyzerBundle\Model\ResultModel;

class PharLoader implements LoaderInterface
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @param SerializerInterface $serializer
     */
    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @inheritDoc
     */
    public function load($filename)
    {
        $data = file_get_contents('phar://' . $filename . '/result.serialized');
        $result = $this->serializer->deserialize($data);

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function supports($filename)
    {
        return 'phar' === pathinfo($filename, PATHINFO_EXTENSION);
    }
}
