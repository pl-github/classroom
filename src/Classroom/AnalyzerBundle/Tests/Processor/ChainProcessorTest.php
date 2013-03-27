<?php

namespace Classroom\AnalyzerBundle\Test\Processor;

use Classroom\AnalyzerBundle\Log\Log;
use Classroom\AnalyzerBundle\Processor\ChainProcessor;
use Classroom\AnalyzerBundle\Result\Result;

class ChainProcessorTest extends \PHPUnit_Framework_TestCase
{
    public function testCountGrades()
    {
        $result = new Result();
        $result->setLog(new Log());

        $processor1 = $this->getMockBuilder('Classroom\AnalyzerBundle\Processor\ProcessorInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $processor1->expects($this->once())
            ->method('process')
            ->with($this->isInstanceOf('Classroom\AnalyzerBundle\Result\Result'));

        $processor2 = $this->getMockBuilder('Classroom\AnalyzerBundle\Processor\ProcessorInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $processor2->expects($this->once())
            ->method('process')
            ->with($this->isInstanceOf('Classroom\AnalyzerBundle\Result\Result'));

        $processor = new ChainProcessor(array($processor1, $processor2));
        $processor->process($result);
    }
}
