<?php

namespace Code\PhpAnalyzerBundle\Phpcs;

use Code\AnalyzerBundle\Analyzer\Runner\RunnerInterface;
use Code\AnalyzerBundle\ProcessExecutor;
use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\Process\ProcessBuilder;
use Symfony\Component\Process\Process;

class PhpcsRunner implements RunnerInterface
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
    public function run($sourceDirectory, $workDirectory)
    {
        $phpcsFilename = $this->ensureDirectoryWritable($workDirectory) . '/phpcs.xml';

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

    /**
     * Ensure directory exists and is writable
     *
     * @param string $directory
     * @return string
     * @throws \Exception
     */
    private function ensureDirectoryWritable($directory)
    {
        if (!file_exists($directory) && !mkdir($directory, 0777, true)) {
            throw new \Exception('Can\'t create data dir');
        }

        if (!is_writable($directory)) {
            throw new \Exception('Data dir not writable');
        }

        return $directory;
    }
}
