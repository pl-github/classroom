<?php

namespace Code\AnalyzerBundle\Writer;

use Code\AnalyzerBundle\Model\NodeInterface;
use Code\AnalyzerBundle\Model\ResultModel;
use Code\AnalyzerBundle\Model\SmellModel;
use Code\AnalyzerBundle\Serializer\SerializerInterface;
use Code\AnalyzerBundle\Source\Storage\PharStorage;

class PharWriter
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
    public function write(ResultModel $result, $targetDir, $baseFilename)
    {
        $filename = $targetDir . '/' . $baseFilename . '.phar';

        $phar = new \Phar($filename);

        foreach ($result->getSources() as $source) {
            $content = $source->getContent();
            $sourceFilename = 'source/' . $source->getHash() . '.sha1';
            $phar->addFromString(
                $sourceFilename,
                $content
            );

            $storage = new PharStorage($sourceFilename);
            $source->setStorage($storage);
        }

        $phar->addFromString('load.php', '<?php return unserialize(file_get_contents(__DIR__ . "/result.serialized"));');

        $xml = $this->serializer->serialize($result);

        $phar->addFromString('result.xml', $xml);

        return $filename;
    }
}
