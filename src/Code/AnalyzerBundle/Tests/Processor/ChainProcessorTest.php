<?php

namespace Code\AnalyzerBundle\Test\Processor;

use Code\AnalyzerBundle\Log\Log;
use Code\AnalyzerBundle\Processor\ChainProcessor;
use Code\AnalyzerBundle\Result\Result;

class ChainProcessorTest extends \PHPUnit_Framework_TestCase
{
    public function testCountGrades()
    {
        $result = new Result();
        $result->setLog(new Log());

        $processor1 = $this->getMockBuilder('Code\AnalyzerBundle\Processor\ProcessorInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $processor1->expects($this->once())
            ->method('process')
            ->with($this->isInstanceOf('Code\AnalyzerBundle\Result\Result'));

        $processor2 = $this->getMockBuilder('Code\AnalyzerBundle\Processor\ProcessorInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $processor2->expects($this->once())
            ->method('process')
            ->with($this->isInstanceOf('Code\AnalyzerBundle\Result\Result'));

        $processor = new ChainProcessor(array($processor1, $processor2));
        $processor->process($result);
    }
}
