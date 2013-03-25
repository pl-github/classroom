<?php

namespace Code\PhpAnalyzerBundle\Tests\Pdepend;

use Code\PhpAnalyzerBundle\Pdepend\PdependCollector;
use org\bovigo\vfs\vfsStream;

class PdependCollectorTest extends \PHPUnit_Framework_TestCase
{
    public function testRun()
    {
        vfsStream::setup('root', 0777, array('sourceDir' => array(), 'workDir' => array()));

        $processExecutorMock = $this->getMockBuilder('Code\AnalyzerBundle\ProcessExecutor')
            ->disableOriginalConstructor()
            ->getMock();

        $loggerMock = $this->getMockBuilder('Symfony\Component\HttpKernel\Log\LoggerInterface')
            ->getMock();

        $collector = new PdependCollector(
            $processExecutorMock,
            $loggerMock,
            'pdepend'
        );

        $processExecutorMock
            ->expects($this->once())
            ->method('execute')
            ->with($this->isInstanceOf('Symfony\Component\Process\Process'));

        $logMock = $this->getMockBuilder('Code\AnalyzerBundle\Log\Log')
            ->disableOriginalConstructor()
            ->getMock();

        $filename = $collector->collect($logMock, 'sourceDir', 'workDir');

        $this->assertEquals($filename, 'workDir/pdepend.xml');
    }
}
