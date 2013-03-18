<?php

namespace Code\CopyPasteDetectionBundle\Tests\Phpcs;

use Code\CopyPasteDetectionBundle\Phpcpd\PhpcpdRunner;
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

        $runner = new PhpcpdRunner(
            $processExecutorMock,
            $loggerMock,
            'phpcs'
        );

        $processExecutorMock
            ->expects($this->once())
            ->method('execute')
            ->with($this->isInstanceOf('Symfony\Component\Process\Process'));

        $filename = $runner->run('sourceDir', 'workDir');

        $this->assertEquals($filename, 'workDir/phpcpd.xml');
    }
}
