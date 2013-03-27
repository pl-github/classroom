<?php

namespace Classroom\PhpAnalyzerBundle\Phpmd;

use Classroom\AnalyzerBundle\Log\Log;
use Classroom\AnalyzerBundle\ProcessExecutor;
use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\Process\ProcessBuilder;
use Symfony\Component\Process\Process;

class PhpmdCollector
{
    /**
     * @var ProcessExecutor
     */
    private $processExecutor;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var string
     */
    private $phpmdExecutable;

    /**
     * @param ProcessExecutor $processExecutor
     * @param LoggerInterface $logger
     * @param string          $phpmdExecutable
     */
    public function __construct(ProcessExecutor $processExecutor, LoggerInterface $logger, $phpmdExecutable)
    {
        $this->processExecutor = $processExecutor;
        $this->logger = $logger;
        $this->phpmdExecutable = $phpmdExecutable;
    }

    /**
     * @inheritDoc
     */
    public function collect(Log $log, $sourceDirectory, $workDirectory)
    {
        $phpmdFilename = $workDirectory . '/phpmd.xml';
        #return $phpmdFilename;

        if (file_exists($phpmdFilename) && !unlink($phpmdFilename)) {
            throw new \Exception('Can\'t unlink ' . $phpmdFilename);
        }

        $processBuilder = new ProcessBuilder();
        $processBuilder->add($this->phpmdExecutable)
            ->add($sourceDirectory)
            ->add('xml')
            ->add('codesize,unusedcode,naming')
            ->add('--suffixes')->add('php')
            ->add('--reportfile')->add($phpmdFilename);

        $process = $processBuilder->getProcess();

        $log->addCommand($process->getCommandLine());
        $this->logger->debug($process->getCommandLine());

        $this->processExecutor->execute($process, 2);

        $log->addOutput($process->getOutput());
        $log->addError($process->getErrorOutput());

        return $phpmdFilename;
    }
}
