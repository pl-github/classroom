<?php

namespace Classroom\AnalyzerBundle\Writer;

use Classroom\AnalyzerBundle\Result\Result;

class DelegatingWriter implements WriterInterface
{
    /**
     * @var WriterResolverInterface
     */
    private $writerResolver;

    /**
     * @param WriterResolverInterface $writerResolver
     */
    public function __construct(WriterResolverInterface $writerResolver)
    {
        $this->writerResolver = $writerResolver;
    }

    /**
     * @inheritDoc
     */
    public function write(Result $result, $filename)
    {
        $writer = $this->writerResolver->resolve($filename);

        if (false === $writer) {
            throw new \Exception('No suitable writer found');
        }

        return $writer->write($result, $filename);
    }

    /**
     * @inheritDoc
     */
    public function supports($filename)
    {
        return false !== $this->writerResolver->resolve($filename);
    }
}
