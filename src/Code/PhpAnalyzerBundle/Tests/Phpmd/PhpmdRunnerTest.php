<?php

namespace Code\PhpAnalyzerBundle\Tests\Phpcs;

use Code\PhpAnalyzerBundle\Phpmd\PhpmdRunner;
use org\bovigo\vfs\vfsStream;

class PhpmdRunnerTest extends \PHPUnit_Framework_TestCase
{
    public function testRun()
    {
        vfsStream::setup('root', 0777, array('sourceDir' => array(), 'workDir' => array()));

        $processExecutorMock = $this->getMockBuilder('Code\AnalyzerBundle\ProcessExecutor')
            ->disableOriginalConstructor()
            ->getMock();

        $loggerMock = $this->getMockBuilder('Symfony\Component\HttpKernel\Log\LoggerInterface')
            ->getMock();

        $runner = new PhpmdRunner(
            $processExecutorMock,
            $loggerMock,
            'phpmd'
        );

        $processExecutorMock
            ->expects($this->once())
            ->method('execute')
            ->with($this->isInstanceOf('Symfony\Component\Process\Process'));

        $filename = $runner->run('sourceDir', 'workDir');

        $this->assertEquals($filename, 'workDir/phpmd.xml');
    }
}
