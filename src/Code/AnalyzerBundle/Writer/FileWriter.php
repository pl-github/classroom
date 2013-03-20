<?php

namespace Code\AnalyzerBundle\Writer;

use Code\AnalyzerBundle\Model\NodeInterface;
use Code\AnalyzerBundle\Model\Reference;
use Code\AnalyzerBundle\Model\ResultModel;
use Code\AnalyzerBundle\Model\SmellModel;
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
    public function write(ResultModel $result, $filename)
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
