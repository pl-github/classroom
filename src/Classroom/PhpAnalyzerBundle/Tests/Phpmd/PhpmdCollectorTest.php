<?php

namespace Classroom\PhpAnalyzerBundle\Tests\Phpmd;

use Classroom\PhpAnalyzerBundle\Phpmd\PhpmdCollector;
use org\bovigo\vfs\vfsStream;

class PhpmdCollectorTest extends \PHPUnit_Framework_TestCase
{
    public function testRun()
    {
        vfsStream::setup('root', 0777, array('sourceDir' => array(), 'workDir' => array()));

        $processExecutorMock = $this->getMockBuilder('Classroom\AnalyzerBundle\ProcessExecutor')
            ->disableOriginalConstructor()
            ->getMock();

        $loggerMock = $this->getMockBuilder('Symfony\Component\HttpKernel\Log\LoggerInterface')
            ->getMock();

        $collector = new PhpmdCollector(
            $processExecutorMock,
            $loggerMock,
            'phpmd'
        );

        $processExecutorMock
            ->expects($this->once())
            ->method('execute')
            ->with($this->isInstanceOf('Symfony\Component\Process\Process'));

        $logMock = $this->getMockBuilder('Classroom\AnalyzerBundle\Log\Log')
            ->disableOriginalConstructor()
            ->getMock();

        $filename = $collector->collect($logMock, 'sourceDir', 'workDir');

        $this->assertEquals($filename, 'workDir/phpmd.xml');
    }
}
