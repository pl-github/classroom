<?php

namespace Code\PhpAnalyzerBundle\Phpcs;

use Code\AnalyzerBundle\Log\Log;
use Code\AnalyzerBundle\ProcessExecutor;
use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\Process\ProcessBuilder;
use Symfony\Component\Process\Process;

class PhpcsCollector
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
    private $phpcsExecutable;

    /**
     * @param ProcessExecutor $processExecutor
     * @param LoggerInterface $logger
     * @param string          $phpcsExecutable
     */
    public function __construct(ProcessExecutor $processExecutor, LoggerInterface $logger, $phpcsExecutable)
    {
        $this->processExecutor = $processExecutor;
        $this->logger = $logger;
        $this->phpcsExecutable = $phpcsExecutable;
    }

    /**
     * @inheritDoc
     */
    public function collect(Log $log, $sourceDirectory, $workDirectory)
    {
        $phpcsFilename = $workDirectory . '/phpcs.xml';
        #return $phpcsFilename;

        if (file_exists($phpcsFilename) && !unlink($phpcsFilename)) {
            throw new \Exception('Can\'t unlink ' . $phpcsFilename);
        }

        $processBuilder = new ProcessBuilder();
        $processBuilder
            ->add($this->phpcsExecutable)
            ->add('--extensions=php')
            ->add('--standard=PSR1')
            ->add('--standard=PSR2')
            ->add('--report-xml=' . $phpcsFilename)
            ->add($sourceDirectory);

        $process = $processBuilder->getProcess();

        $log->addCommand($process->getCommandLine());
        $this->logger->debug($process->getCommandLine());

        $this->processExecutor->execute($process, 1);

        $log->addOutput($process->getOutput());
        $log->addError($process->getErrorOutput());

        return $phpcsFilename;
    }
}
