<?php

namespace Classroom\AnalyzerBundle\Writer;

class WriterResolver implements WriterResolverInterface
{
    /**
     * @var WriterInterface[]
     */
    private $writers;

    /**
     * @param WriterInterface[] $writers
     */
    public function __construct(array $writers)
    {
        $this->writers = $writers;
    }

    /**
     * @inheritDoc
     */
    public function resolve($filename)
    {
        foreach ($this->writers as $writer) {
            if ($writer->supports($filename)) {
                return $writer;
            }
        }

        return false;
    }
}
