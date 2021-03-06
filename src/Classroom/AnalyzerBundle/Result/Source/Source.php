<?php

namespace Classroom\AnalyzerBundle\Result\Source;

use Classroom\AnalyzerBundle\Result\Source\Storage\StorageInterface;

class Source implements SourceInterface
{
    /**
     * @var StorageInterface
     */
    private $storage;

    /**
     * @var string
     */
    private $hash;

    /**
     * @param StorageInterface $storage
     */
    public function __construct(StorageInterface $storage)
    {
        $this->storage = $storage;

        $this->hash = sha1(uniqid('bla', true) . rand(0, 99999));
    }

    /**
     * @inheritDoc
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * @inheritDoc
     */
    public function setHash($hash)
    {
        $this->hash = $hash;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setStorage(StorageInterface $storage)
    {
        $this->storage = $storage;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getStorage()
    {
        return $this->storage;
    }

    /*+
     * @inheritDoc
     */
    public function getContent()
    {
        return $this->storage->getContent();
    }

    /*+
     * @inheritDoc
     */
    public function getContentAsArray()
    {
        return $this->storage->getContentAsArray();
    }

    /**
     * @inheritDoc
     */
    public function getRange(SourceRange $range)
    {
        return implode(PHP_EOL, $this->getRangeAsArray($range));
    }

    /**
     * @inheritDoc
     */
    public function getRangeAsArray(SourceRange $range)
    {
        $contentLines = $this->getContentAsArray();
        $beginLine = $range->getBeginLine() - 1;
        $endLine = $range->getEndLine();
        $length = $endLine - $beginLine;

        return array_slice($contentLines, $beginLine, $length, true);
    }
}
