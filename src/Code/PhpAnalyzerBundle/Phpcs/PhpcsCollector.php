<?php

namespace Code\PhpAnalyzerBundle\Phpcs;

use Code\AnalyzerBundle\Analyzer\Collector\CollectorInterface;
use Code\AnalyzerBundle\ProcessExecutor;
use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\Process\ProcessBuilder;
use Symfony\Component\Process\Process;

class PhpcsCollector implements CollectorInterface
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
    public function collect($sourceDirectory, $workDirectory)
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

        $this->logger->debug($process->getCommandLine());

        $this->processExecutor->execute($process, 1);

        return $phpcsFilename;
    }
}
