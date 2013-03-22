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
            $msg = 'PharWriter needs PHAR write support enabled. Set phar.readonly = Off in your php.ini';
            throw new \Exception($msg);
        }

        if (file_exists($filename)) {
            unlink($filename);
        }

        $phar = new \Phar($filename);
        $phar->startBuffering();

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

        $phar->stopBuffering();

        if (\Phar::canCompress(\Phar::BZ2)) {
            $phar->compress(\Phar::BZ2);
            rename($filename . '.bz2', $filename);
        } elseif (\Phar::canCompress(\Phar::GZ)) {
            $phar->compress(\Phar::GZ);
            rename($filename . '.gz', $filename);
        }

        unset($phar);

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
