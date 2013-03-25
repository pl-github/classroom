<?php

namespace Code\PhpAnalyzerBundle\Tests\Phpcs;

use Code\PhpAnalyzerBundle\Phpcpd\PhpcpdCollector;
use org\bovigo\vfs\vfsStream;

class PhpcpdCollectorTest extends \PHPUnit_Framework_TestCase
{
    public function testRun()
    {
        vfsStream::setup('root', 0777, array('sourceDir' => array(), 'workDir' => array()));

        $processExecutorMock = $this->getMockBuilder('Code\AnalyzerBundle\ProcessExecutor')
            ->disableOriginalConstructor()
            ->getMock();

        $loggerMock = $this->getMockBuilder('Symfony\Component\HttpKernel\Log\LoggerInterface')
            ->getMock();

        $collector = new PhpcpdCollector(
            $processExecutorMock,
            $loggerMock,
            'phpcs'
        );

        $processExecutorMock
            ->expects($this->once())
            ->method('execute')
            ->with($this->isInstanceOf('Symfony\Component\Process\Process'));

        $logMock = $this->getMockBuilder('Code\AnalyzerBundle\Log\Log')
            ->disableOriginalConstructor()
            ->getMock();

        $filename = $collector->collect($logMock, 'sourceDir', 'workDir');

        $this->assertEquals($filename, 'workDir/phpcpd.xml');
    }
}
