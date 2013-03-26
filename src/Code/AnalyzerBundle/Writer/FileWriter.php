<?php

namespace Code\AnalyzerBundle\Writer;

use Code\AnalyzerBundle\Result\Result;
use Code\AnalyzerBundle\Serializer\SerializerInterface;

class FileWriter implements WriterInterface
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
    public function write(Result $result, $filename)
    {
        $data = $this->serializer->serialize($result);

        file_put_contents($filename, $data);

        return $filename;
    }

    /**
     * @inheritDoc
     */
    public function supports($filename)
    {
        return $this->serializer->getType() === pathinfo($filename, PATHINFO_EXTENSION);
    }
}
