<?php

namespace Code\AnalyzerBundle\Analyzer;

use Code\AnalyzerBundle\Analyzer\Runner\RunnerInterface;
use Code\AnalyzerBundle\Analyzer\Processor\ProcessorInterface;
use Code\AnalyzerBundle\ResultBuilderInterface;

class Analyzer implements AnalyzerInterface
{
    /**
     * @var RunnerInterface
     */
    private $runner;

    /**
     * @var ProcessorInterface
     */
    private $processor;

    /**
     * @param RunnerInterface    $runner
     * @param ProcessorInterface $processor
     */
    public function __construct(RunnerInterface $runner, ProcessorInterface $processor)
    {
        $this->runner    = $runner;
        $this->processor = $processor;
    }

    /**
     * @inheritDoc
     */
    public function analyze(ResultBuilderInterface $resultBuilder, $sourceDirectory, $workDirectory)
    {
        $filename = $this->runner->run($sourceDirectory, $workDirectory);
        $classes = $this->processor->process($resultBuilder, $filename);

        return $classes;
    }
}
