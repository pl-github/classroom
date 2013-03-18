<?php

namespace Code\PhpAnalyzerBundle\Phpmd;

use Code\AnalyzerBundle\Analyzer\Runner\RunnerInterface;
use Code\AnalyzerBundle\ProcessExecutor;
use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\Process\ProcessBuilder;
use Symfony\Component\Process\Process;

class PhpmdRunner implements RunnerInterface
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
    public function run($sourceDirectory, $workDirectory)
    {
        $phpmdFilename = $this->ensureDirectoryWritable($workDirectory) . '/phpmd.xml';

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

        $this->logger->debug($process->getCommandLine());

        $this->processExecutor->execute($process, 2);

        return $phpmdFilename;
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
