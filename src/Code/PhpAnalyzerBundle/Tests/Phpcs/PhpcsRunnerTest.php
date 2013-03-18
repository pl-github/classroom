<?php

namespace Code\CodeStyleBundle\Tests\Phpcs;

use Code\CodeStyleBundle\Phpcs\PhpcsRunner;
use org\bovigo\vfs\vfsStream;

class PhpcpdRunnerTest extends \PHPUnit_Framework_TestCase
{
    public function testRun()
    {
        vfsStream::setup('root', 0777, array('sourceDir' => array(), 'workDir' => array()));

        $processExecutorMock = $this->getMockBuilder('Code\AnalyzerBundle\ProcessExecutor')
            ->disableOriginalConstructor()
            ->getMock();

        $loggerMock = $this->getMockBuilder('Symfony\Component\HttpKernel\Log\LoggerInterface')
            ->getMock();

        $runner = new PhpcsRunner(
            $processExecutorMock,
            $loggerMock,
            'phpcs'
        );

        $processExecutorMock
            ->expects($this->once())
            ->method('execute')
            ->with($this->isInstanceOf('Symfony\Component\Process\Process'));

        $filename = $runner->run('sourceDir', 'workDir');

        $this->assertEquals($filename, 'workDir/phpcs.xml');
    }
}