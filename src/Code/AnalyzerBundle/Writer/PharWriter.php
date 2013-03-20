<?php

namespace Code\AnalyzerBundle\Writer;

use Code\AnalyzerBundle\Model\NodeInterface;
use Code\AnalyzerBundle\Model\ResultModel;
use Code\AnalyzerBundle\Model\SmellModel;
use Code\AnalyzerBundle\Serializer\SerializerInterface;
use Code\AnalyzerBundle\Source\Storage\FilesystemStorage;

class PharWriter implements WriterInterface
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
        if (!\Phar::canWrite()) {
            throw new \Exception('PharWriter needs PHAR write support enabled. Set phar.readonly = Off in your php.ini');
        }

        if (file_exists($filename)) {
            unlink($filename);
        }

        $phar = new \Phar($filename);

        foreach ($result->getSources() as $source) {
            $storage = $source->getStorage();
            $sourceFilename = 'source/' . $source->getHash() . '.txt';
            switch (get_class($storage)) {
                case 'Code\AnalyzerBundle\Source\Storage\FilesystemStorage':
                    $phar->addFile($storage->getFilename(), $sourceFilename);
                    $storage->setFilename($sourceFilename);
                    break;
                case 'Code\AnalyzerBundle\Source\Storage\StringStorage':
                    $content = $source->getContent();
                    $phar->addFromString(
                        $sourceFilename,
                        $content
                    );
                    $storage = new FilesystemStorage($sourceFilename);
                    $source->setStorage($storage);
                    break;
                default:
                    throw new \Exception('Unknown storage.');
            }
        }

        foreach ($result->getArtifacts() as $artifact) {
            $phar->addFile($artifact, 'artifact/' . basename($artifact));
        }

        $data = $this->serializer->serialize($result);

        $phar->addFromString('result.' . $this->serializer->getType(), $data);

        if (\Phar::canCompress(\Phar::BZ2)) {
            $phar->compressFiles(\Phar::BZ2);
        } elseif (\Phar::canCompress(\Phar::GZ)) {
            $phar->compressFiles(\Phar::GZ);
        }

        return $filename;
    }

    /**
     * @inheritDoc
     */
    public function supports($filename)
    {
        return 'phar' === pathinfo($filename, PATHINFO_EXTENSION);
    }
}
