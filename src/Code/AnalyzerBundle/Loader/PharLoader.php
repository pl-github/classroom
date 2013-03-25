<?php

namespace Code\AnalyzerBundle\Loader;

use Code\AnalyzerBundle\Result\Result;
use Code\AnalyzerBundle\Serializer\SerializerInterface;

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
        $pharFilename = 'phar://' . $filename;

        $data = file_get_contents($pharFilename . '/result.' . $this->serializer->getType());
        $result = $this->serializer->deserialize($data);
        /* @var $result Result */

        foreach ($result->getSources() as $source) {
            $source->getStorage()->setFilename($pharFilename . '/' . $source->getStorage()->getFilename());
        }

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
