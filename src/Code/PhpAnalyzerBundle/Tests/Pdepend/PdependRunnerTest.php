<?php

namespace Code\PhpAnalyzerBundle\Tests\Pdepend;

use Code\PhpAnalyzerBundle\Pdepend\PdependRunner;
use org\bovigo\vfs\vfsStream;

class PdependRunnerTest extends \PHPUnit_Framework_TestCase
{
    public function testRun()
    {
        vfsStream::setup('root', 0777, array('sourceDir' => array(), 'workDir' => array()));

        $processExecutorMock = $this->getMockBuilder('Code\AnalyzerBundle\ProcessExecutor')
            ->disableOriginalConstructor()
            ->getMock();

        $loggerMock = $this->getMockBuilder('Symfony\Component\HttpKernel\Log\LoggerInterface')
            ->getMock();

        $runner = new PdependRunner(
            $processExecutorMock,
            $loggerMock,
            'pdepend'
        );

        $processExecutorMock
            ->expects($this->once())
            ->method('execute')
            ->with($this->isInstanceOf('Symfony\Component\Process\Process'));

        $filename = $runner->run('sourceDir', 'workDir');

        $this->assertEquals($filename, 'workDir/pdepend.xml');
    }
}
