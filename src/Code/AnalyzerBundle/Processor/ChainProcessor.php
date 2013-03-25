<?php

namespace Code\AnalyzerBundle\Processor;

use Code\AnalyzerBundle\Result\Result;

class ChainProcessor implements ProcessorInterface
{
    /**
     * @var ProcessorInterface[]
     */
    private $processors;

    /**
     * @param ProcessorInterface[] $processors
     */
    public function __construct(array $processors)
    {
        $this->processors = $processors;
    }

    /**
     * @inheritDoc
     */
    public function process(Result $result)
    {
        foreach ($this->processors as $processor) {
            $processor->process($result);
        }
    }
}