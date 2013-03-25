<?php

namespace Code\AnalyzerBundle\Test\PostProcessor;

use Code\AnalyzerBundle\Log\Log;
use Code\AnalyzerBundle\PostProcessor\ChainPostProcessor;
use Code\AnalyzerBundle\PostProcessor\GpaPostProcessor;
use Code\AnalyzerBundle\Result\Result;

class ChainPostProcessorTest extends \PHPUnit_Framework_TestCase
{
    public function testCountGrades()
    {
        $result = new Result();
        $result->setLog(new Log());

        $postProcessor1 = $this->getMockBuilder('Code\AnalyzerBundle\PostProcessor\PostProcessorInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $postProcessor1->expects($this->once())
            ->method('postProcess')
            ->with($this->isInstanceOf('Code\AnalyzerBundle\Result\Result'));

        $postProcessor2 = $this->getMockBuilder('Code\AnalyzerBundle\PostProcessor\PostProcessorInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $postProcessor2->expects($this->once())
            ->method('postProcess')
            ->with($this->isInstanceOf('Code\AnalyzerBundle\Result\Result'));

        $postProcessor = new ChainPostProcessor(array($postProcessor1, $postProcessor2));
        $postProcessor->postProcess($result);
    }
}
