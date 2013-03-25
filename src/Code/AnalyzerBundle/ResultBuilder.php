<?php

namespace Code\AnalyzerBundle;

use Code\AnalyzerBundle\PreProcessor\PreProcessorInterface;
use Code\AnalyzerBundle\Processor\ProcessorInterface;
use Code\AnalyzerBundle\PostProcessor\PostProcessorInterface;
use Code\AnalyzerBundle\Log\Log;
use Code\AnalyzerBundle\Result\Result;

class ResultBuilder implements ResultBuilderInterface
{
    /**
     * @var PostProcessorInterface
     */
    private $preProcessor;

    /**
     * @var PostProcessorInterface
     */
    private $processor;

    /**
     * @var PostProcessorInterface
     */
    private $postProcessor;

    /**
     * @param PreProcessorInterface $postProcessor
     * @param ProcessorInterface $postProcessor
     * @param PostProcessorInterface $postProcessor
     */
    public function __construct(PreProcessorInterface $preProcessor,
                                ProcessorInterface $processor,
                                PostProcessorInterface $postProcessor)
    {
        $this->preProcessor = $preProcessor;
        $this->processor = $processor;
        $this->postProcessor = $postProcessor;
    }

    /**
     * @inheritDoc
     */
    public function buildResult($sourceDirectory, $workingDirectory, callable $logCallback = null)
    {
        $result = new Result();
        $result->setLog(new Log($logCallback));
        $result->setSourceDirectory($sourceDirectory);
        $result->setWorkingDirectory($workingDirectory);

        $this->preProcessor->process($result);
        $this->processor->process($result);
        $this->postProcessor->process($result);

        $result->setBuiltAt(new \DateTime());

        return $result;
    }
}
