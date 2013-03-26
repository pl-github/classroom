<?php

namespace Code\AnalyzerBundle\Loader;

use Code\AnalyzerBundle\Serializer\SerializerInterface;

class FileLoader implements LoaderInterface
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
        $data = file_get_contents($filename);
        $result = $this->serializer->deserialize($data);

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function supports($filename)
    {
        return $this->serializer->getType() === pathinfo($filename, PATHINFO_EXTENSION);
    }
}
