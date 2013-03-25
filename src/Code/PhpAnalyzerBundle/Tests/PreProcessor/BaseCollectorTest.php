<?php

namespace Code\PhpAnalyzerBundle\Tests\Base;

use Code\PhpAnalyzerBundle\PreProcessor\BaseCollector;
use org\bovigo\vfs\vfsStream;

class BaseCollectorTest extends \PHPUnit_Framework_TestCase
{
    public function testRun()
    {
        vfsStream::setup('root', 0777, array('sourceDir' => array('file1' => 'file1', 'file2' => 'file2')));

        $loggerMock = $this->getMockBuilder('Symfony\Component\HttpKernel\Log\LoggerInterface')
            ->getMock();

        $collector = new BaseCollector($loggerMock);

        $logMock = $this->getMockBuilder('Code\AnalyzerBundle\Log\Log')
            ->disableOriginalConstructor()
            ->getMock();

        $files = $collector->collect($logMock, vfsStream::url('root/sourceDir'), 'workDir');

        $this->assertEquals($files, array());
    }
}
