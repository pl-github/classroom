<?php

namespace Classroom\AnalyzerBundle\Writer;

use Classroom\AnalyzerBundle\Result\Result;
use Classroom\AnalyzerBundle\Result\Smell\SmellModel;
use Classroom\AnalyzerBundle\Result\Source\Storage\FilesystemStorage;
use Classroom\AnalyzerBundle\Serializer\SerializerInterface;

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
    public function write(Result $result, $filename)
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

        // fix sources for phar inclusion
        foreach ($result->getSources() as $source) {
            $storage = $source->getStorage();
            $sourceFilename = 'source/' . $source->getHash() . '.txt';
            switch (get_class($storage)) {
                case 'Classroom\AnalyzerBundle\Result\Source\Storage\FilesystemStorage':
                    $phar->addFile($storage->getFilename(), $sourceFilename);
                    $storage->setFilename($sourceFilename);
                    break;
                case 'Classroom\AnalyzerBundle\Result\Source\Storage\StringStorage':
                    $content = $source->getContent();
                    $phar->addFromString(
                        $sourceFilename,
                        $content
                    );
                    $storage = new FilesystemStorage($sourceFilename);
                    $source->setStorage($storage);
                    break;
                default:
                    throw new \Exception('Unknown storage ' . get_class($storage));
            }
        }

        // add artifacts
        foreach ($result->getArtifacts() as $artifact) {
            $phar->addFile($artifact, 'artifact/' . basename($artifact));
        }

        // add log as artifact
        $lines = array();
        foreach ($result->getLog()->getItems() as $item) {
            $lines[] = $item['line'];
        }
        $phar->addFromString('artifact/result.log', implode(PHP_EOL, $lines));

        // serialize and add result data
        $data = $this->serializer->serialize($result);
        $phar->addFromString('result.' . $this->serializer->getType(), $data);

        $phar->stopBuffering();

        // compress phar, normalize filename
        if (\Phar::canCompress(\Phar::BZ2)) {
            $phar->compress(\Phar::BZ2);
            unset($phar);
            rename($filename . '.bz2', $filename);
        } elseif (\Phar::canCompress(\Phar::GZ)) {
            $phar->compress(\Phar::GZ);
            unset($phar);
            rename($filename . '.gz', $filename);
        } else {
            unset($phar);
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
