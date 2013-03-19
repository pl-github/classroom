<?php

namespace Code\AnalyzerBundle\Analyzer;

use Code\AnalyzerBundle\Analyzer\Collector\CollectorInterface;
use Code\AnalyzerBundle\Analyzer\Processor\ProcessorInterface;
use Code\AnalyzerBundle\Model\ResultModel;

class Analyzer implements AnalyzerInterface
{
    /**
     * @var CollectorInterface
     */
    private $collector;

    /**
     * @var ProcessorInterface
     */
    private $processor;

    /**
     * @param CollectorInterface $collector
     * @param ProcessorInterface $processor
     */
    public function __construct(CollectorInterface $collector, ProcessorInterface $processor)
    {
        $this->collector = $collector;
        $this->processor = $processor;
    }

    /**
     * @inheritDoc
     */
    public function analyze(ResultModel $result, $sourceDirectory, $workDirectory)
    {
        $data = $this->collector->collect($sourceDirectory, $workDirectory);
        $classes = $this->processor->process($result, $data);

        return $classes;
    }
}
