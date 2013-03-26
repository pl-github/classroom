<?php

namespace Classroom\AnalyzerBundle;

use Classroom\AnalyzerBundle\PreProcessor\PreProcessorInterface;
use Classroom\AnalyzerBundle\Processor\ProcessorInterface;
use Classroom\AnalyzerBundle\PostProcessor\PostProcessorInterface;
use Classroom\AnalyzerBundle\Log\Log;
use Classroom\AnalyzerBundle\Result\Result;

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
